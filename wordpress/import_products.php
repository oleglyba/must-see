<?php
/**
 * Bulk import products from WooCommerce CSV format.
 * Run via: wp eval-file import_products.php --path=/path/to/wordpress
 */

if (!defined('ABSPATH')) {
    echo "Must be run via WP-CLI: wp eval-file import_products.php\n";
    exit(1);
}

$csv_file = '/Users/oleh/Desktop/woo_import.csv';
$batch_size = 100;

if (!file_exists($csv_file)) {
    echo "CSV file not found: $csv_file\n";
    exit(1);
}

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
if ($skipped > 0) {
    echo "Skipped $skipped malformed rows\n";
}

$total = count($rows);
echo "Total products to import: $total\n";

// Cache categories to avoid duplicate lookups
$cat_cache = [];

function get_or_create_category($name) {
    global $cat_cache;
    if (empty($name)) return 0;

    if (isset($cat_cache[$name])) {
        return $cat_cache[$name];
    }

    $term = get_term_by('name', $name, 'product_cat');
    if ($term) {
        $cat_cache[$name] = $term->term_id;
        return $term->term_id;
    }

    $result = wp_insert_term($name, 'product_cat');
    if (is_wp_error($result)) {
        echo "Error creating category '$name': " . $result->get_error_message() . "\n";
        return 0;
    }

    $cat_cache[$name] = $result['term_id'];
    return $result['term_id'];
}

$imported = 0;
$errors = 0;
$start_time = time();

foreach ($rows as $i => $row) {
    $name = trim($row['Name'] ?? '');
    if (empty($name)) {
        $errors++;
        continue;
    }

    // Create product post
    $post_data = [
        'post_title'   => $name,
        'post_content' => $row['Description'] ?? '',
        'post_excerpt' => $row['Short description'] ?? '',
        'post_status'  => 'publish',
        'post_type'    => 'product',
    ];

    $product_id = wp_insert_post($post_data, true);
    if (is_wp_error($product_id)) {
        echo "Error importing '$name': " . $product_id->get_error_message() . "\n";
        $errors++;
        continue;
    }

    // Set product type
    wp_set_object_terms($product_id, 'simple', 'product_type');

    // Set category
    $cat_name = trim($row['Categories'] ?? '');
    if ($cat_name) {
        $cat_id = get_or_create_category($cat_name);
        if ($cat_id) {
            wp_set_object_terms($product_id, [$cat_id], 'product_cat');
        }
    }

    // Set prices
    $regular_price = $row['Regular price'] ?? '';
    $sale_price = $row['Sale price'] ?? '';

    update_post_meta($product_id, '_regular_price', $regular_price);
    if (!empty($sale_price)) {
        update_post_meta($product_id, '_sale_price', $sale_price);
        update_post_meta($product_id, '_price', $sale_price);
    } else {
        update_post_meta($product_id, '_price', $regular_price);
    }

    // Set stock
    update_post_meta($product_id, '_stock_status', 'instock');
    update_post_meta($product_id, '_manage_stock', 'no');

    // Set visibility
    update_post_meta($product_id, '_visibility', 'visible');

    // Set image URL as external (don't download)
    $image_url = trim($row['Images'] ?? '');
    if ($image_url) {
        update_post_meta($product_id, '_product_image_url', $image_url);
        // Store as product gallery placeholder
        update_post_meta($product_id, '_external_image', $image_url);
    }

    $imported++;

    // Progress
    if ($imported % $batch_size === 0) {
        $elapsed = time() - $start_time;
        $rate = $elapsed > 0 ? round($imported / $elapsed, 1) : 0;
        echo "Imported: $imported / $total ($rate/sec)\n";
        // Clear object cache to prevent memory issues
        wp_cache_flush();
    }
}

$elapsed = time() - $start_time;
echo "\n=== Import Complete ===\n";
echo "Imported: $imported\n";
echo "Errors: $errors\n";
echo "Time: {$elapsed}s\n";
echo "Categories created: " . count($cat_cache) . "\n";
