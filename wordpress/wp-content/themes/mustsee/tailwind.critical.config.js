/**
 * Critical (above-the-fold) Tailwind build.
 * Scans only the markup that renders on the first screen so the inlined
 * <style> in <head> stays tiny and kills render-blocking CSS.
 * The full bundle (tailwind.config.js) is loaded deferred.
 */
const base = require("./tailwind.config.js");

module.exports = {
  ...base,
  content: [
    "./header.php",
    "./template-parts/hero.php",
    "./template-parts/search-bar.php",
    "./template-parts/logo.php",
    "./template-parts/socials.php",
    "./template-parts/tour-card.php",
    "./template-parts/article-card.php",
  ],
};
