<?php
/**
 * Plugin.
 *
 * @wp-plugin
 *
 * Version: 160722.57918
 * Text Domain: pre-serve
 * Plugin Name: &lt;Pre&gt;serve Pro
 *
 * Author: WP Sharks™
 * Author URI: https://wpsharks.com
 *
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Plugin URI: https://wpsharks.com/product/pre-serve-pro
 * Description: Preserves content inside &lt;pre&gt;, &lt;code&gt;, or &lt;samp&gt; tags.
 */
// PHP v5.2 compatible.

if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
require dirname(__FILE__).'/src/includes/wp-php-rv.php';

if (require(dirname(__FILE__).'/src/vendor/websharks/wp-php-rv/src/includes/check.php')) {
    require_once dirname(__FILE__).'/src/includes/plugin.php';
} else {
    wp_php_rv_notice('<Pre>serve Pro');
}
