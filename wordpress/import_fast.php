<?php
/**
 * Fast bulk import products using direct DB queries.
 * Run: wp eval-file import_fast.php --path=/path/to/wordpress
 */

if (!defined('ABSPATH')) {
    echo "Must be run via WP-CLI\n";
    exit(1);
}

global $wpdb;

$csv_file = '/Users/oleh/Desktop/woo_import.csv';
$batch_size = 500;

// Disable autocommit for speed
$wpdb->query('SET autocommit = 0');
$wpdb->query('SET unique_checks = 0');
$wpdb->query('SET foreign_key_checks = 0');

// Disable term counting during import
wp_defer_term_counting(true);
wp_defer_comment_counting(true);

// Remove all actions that slow down wp_insert_post
remove_all_actions('transition_post_status');
remove_all_actions('save_post');
remove_all_actions('wp_insert_post');
remove_all_actions('save_post_product');
remove_all_actions('woocommerce_new_product');
remove_all_actions('woocommerce_update_product');

// Read CSV
$handle = fopen($csv_file, 'r');
$headers = fgetcsv($handle);
$header_count = count($headers);
$rows = [];
$skipped = 0;
while (($data = fgetcsv($handle)) !== false) {
    if (count($data) !== $header_count) {
        $skipped++;
        continue;
    }
    $rows[] = array_combine($headers, $data);
}
fclose($handle);

$total = count($rows);
echo "Total: $total (skipped $skipped malformed)\n";

// Pre-create all categories
$categories = array_unique(array_filter(array_map(function($r) {
    return trim($r['Categories'] ?? '');
}, $rows)));

$cat_map = [];
foreach ($categories as $cat_name) {
    if (empty($cat_name)) continue;
    $term = get_term_by('name', $cat_name, 'product_cat');
    if ($term) {
        $cat_map[$cat_name] = $term->term_id;
    } else {
        $result = wp_insert_term($cat_name, 'product_cat');
        if (!is_wp_error($result)) {
            $cat_map[$cat_name] = $result['term_id'];
        }
    }
}
echo "Categories created: " . count($cat_map) . "\n";

// Get the simple product type term
$simple_term = get_term_by('slug', 'simple', 'product_type');
$simple_term_id = $simple_term ? $simple_term->term_id : 0;

$imported = 0;
$errors = 0;
$start = microtime(true);

// Meta keys we need
$meta_keys = ['_regular_price', '_sale_price', '_price', '_stock_status', '_manage_stock', '_visibility', '_product_image_url', '_external_image'];

foreach (array_chunk($rows, $batch_size) as $chunk_idx => $chunk) {
    // Build batch of posts
    $post_values = [];
    $post_params = [];
    $now = current_time('mysql');
    $now_gmt = current_time('mysql', 1);

    foreach ($chunk as $row) {
        $name = trim($row['Name'] ?? '');
        if (empty($name)) {
            $errors++;
            continue;
        }

        $slug = sanitize_title($name);
        $description = $row['Description'] ?? '';
        $short_desc = $row['Short description'] ?? '';

        $post_values[] = "(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)";
        $post_params[] = 0; // post_author
        $post_params[] = $now;
        $post_params[] = $now_gmt;
        $post_params[] = $description;
        $post_params[] = $name;
        $post_params[] = $short_desc;
        $post_params[] = 'publish';
        $post_params[] = $slug;
        $post_params[] = 'product';
        $post_params[] = ''; // guid - will update later
    }

    if (empty($post_values)) continue;

    // Insert posts
    $sql = "INSERT INTO {$wpdb->posts} (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, post_name, post_type, guid) VALUES " . implode(',', $post_values);
    $wpdb->query($wpdb->prepare($sql, ...$post_params));

    // Get inserted IDs
    $first_id = $wpdb->insert_id;
    $count_inserted = count($post_values);

    // Now add meta and terms for each
    $meta_values = [];
    $meta_params = [];
    $term_rel_values = [];
    $term_rel_params = [];

    $post_id = $first_id;
    $chunk_idx2 = 0;
    foreach ($chunk as $row) {
        $name = trim($row['Name'] ?? '');
        if (empty($name)) continue;

        $regular_price = $row['Regular price'] ?? '';
        $sale_price = $row['Sale price'] ?? '';
        $price = !empty($sale_price) ? $sale_price : $regular_price;
        $image_url = trim($row['Images'] ?? '');

        // Build meta inserts
        $metas = [
            '_regular_price' => $regular_price,
            '_price' => $price,
            '_stock_status' => 'instock',
            '_manage_stock' => 'no',
            '_visibility' => 'visible',
        ];
        if (!empty($sale_price)) {
            $metas['_sale_price'] = $sale_price;
        }
        if (!empty($image_url)) {
            $metas['_product_image_url'] = $image_url;
            $metas['_external_image'] = $image_url;
        }

        foreach ($metas as $key => $val) {
            $meta_values[] = "(%d, %s, %s)";
            $meta_params[] = $post_id;
            $meta_params[] = $key;
            $meta_params[] = $val;
        }

        // Product type term
        if ($simple_term_id) {
            $term_rel_values[] = "(%d, %d, %d)";
            $term_rel_params[] = $post_id;
            $term_rel_params[] = $simple_term_id;
            $term_rel_params[] = 0;
        }

        // Category term
        $cat_name = trim($row['Categories'] ?? '');
        if (!empty($cat_name) && isset($cat_map[$cat_name])) {
            $cat_term = get_term($cat_map[$cat_name], 'product_cat');
            if ($cat_term) {
                $term_rel_values[] = "(%d, %d, %d)";
                $term_rel_params[] = $post_id;
                $term_rel_params[] = $cat_term->term_taxonomy_id;
                $term_rel_params[] = 0;
            }
        }

        $post_id++;
        $chunk_idx2++;
    }

    // Insert meta in batch
    if (!empty($meta_values)) {
        $meta_chunks = array_chunk($meta_values, 1000);
        $meta_param_size = 3; // params per value
        $offset = 0;
        foreach ($meta_chunks as $mc) {
            $count = count($mc);
            $params = array_slice($meta_params, $offset * $meta_param_size, $count * $meta_param_size);
            $sql = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES " . implode(',', $mc);
            $wpdb->query($wpdb->prepare($sql, ...$params));
            $offset += $count;
        }
    }

    // Insert term relationships in batch
    if (!empty($term_rel_values)) {
        $sql = "INSERT IGNORE INTO {$wpdb->term_relationships} (object_id, term_taxonomy_id, term_order) VALUES " . implode(',', $term_rel_values);
        $wpdb->query($wpdb->prepare($sql, ...$term_rel_params));
    }

    // Commit batch
    $wpdb->query('COMMIT');

    $imported += $chunk_idx2;
    $elapsed = microtime(true) - $start;
    $rate = $elapsed > 0 ? round($imported / $elapsed, 1) : 0;
    echo "Imported: $imported / $total ({$rate}/sec)\n";

    // Flush caches
    wp_cache_flush();
}

// Re-enable
$wpdb->query('SET autocommit = 1');
$wpdb->query('SET unique_checks = 1');
$wpdb->query('SET foreign_key_checks = 1');
wp_defer_term_counting(false);
wp_defer_comment_counting(false);

// Update term counts
foreach ($cat_map as $name => $term_id) {
    wp_update_term_count_now([$term_id], 'product_cat');
}
if ($simple_term_id) {
    wp_update_term_count_now([$simple_term_id], 'product_type');
}

$elapsed = round(microtime(true) - $start, 1);
echo "\n=== Done ===\n";
echo "Imported: $imported\n";
echo "Errors: $errors\n";
echo "Time: {$elapsed}s\n";
