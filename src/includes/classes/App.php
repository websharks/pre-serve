<?php
/**
 * Application.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Preserve\Classes;

use WebSharks\WpSharks\Preserve\Classes;
use WebSharks\WpSharks\Preserve\Interfaces;
use WebSharks\WpSharks\Preserve\Traits;
#
use WebSharks\WpSharks\Preserve\Classes\AppFacades as a;
use WebSharks\WpSharks\Preserve\Classes\SCoreFacades as s;
use WebSharks\WpSharks\Preserve\Classes\CoreFacades as c;
#
use WebSharks\WpSharks\Core\Classes as SCoreClasses;
use WebSharks\WpSharks\Core\Interfaces as SCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as SCoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Application.
 *
 * @since 160722.57589 Initial release.
 */
class App extends SCoreClasses\App
{
    /**
     * Version.
     *
     * @since 160722.57589 Initial release.
     *
     * @var string Version.
     */
    const VERSION = '160919.19112'; //v//

    /**
     * Constructor.
     *
     * @since 160722.57589 Initial release.
     *
     * @param array $instance Instance args.
     */
    public function __construct(array $instance = [])
    {
        $instance_base = [
            '©di' => [
                '©default_rule' => [
                    'new_instances' => [
                    ],
                ],
            ],

            '§specs' => [
                '§type' => 'plugin',
                '§file' => dirname(__FILE__, 4).'/plugin.php',
            ],
            '©brand' => [
                '©acronym' => 'PREServe',
                '©name'    => '<Pre>serve',

                '©slug' => 'pre-serve',
                '©var'  => 'pre_serve',

                '©short_slug' => 'pre-serve',
                '©short_var'  => 'pre_serve',

                '©text_domain' => 'pre-serve',
            ],

            '§pro_option_keys' => [],
            '§default_options' => [],
        ];
        parent::__construct($instance_base, $instance);
    }

    /**
     * Other hook setup handler.
     *
     * @since 160722.57589 Initial release.
     */
    protected function onSetupOtherHooks()
    {
        parent::onSetupOtherHooks();

        # On `init` w/ a late priority.

        add_action('init', function () {

            # Filters `the_content`. Covers most WP themes/plugins.

            add_filter('the_content', [$this->Utils->Content, 'onTheContentPreserve'], -1000);
            add_filter('the_content', [$this->Utils->Content, 'onTheContentRestore'], 1000);

            # Filters `get_the_excerpt`. For WP themes/plugins that use a true excerpt.

            add_filter('get_the_excerpt', [$this->Utils->Content, 'onTheContentPreserve'], -1000);
            add_filter('get_the_excerpt', [$this->Utils->Content, 'onTheContentRestore'], 1000);

            # Filters `if_shortcode_content` for compatibility w/ the `[if]` shortcode.

            add_filter('if_shortcode_content', [$this->Utils->Content, 'onTheContentPreserve'], -1000);
            add_filter('if_shortcode_content', [$this->Utils->Content, 'onTheContentRestore'], 1000);

            # Filters `woocommerce_short_description` for compatibility w/ WooCommerce.

            if (has_filter('woocommerce_short_description', 'wc_format_product_short_description') && s::jetpackCanMarkdown()) {
                // Fixing a WooCommerce bug by Moving Jetpack markdown to a more logical/compatible priority.
                // I consider this a bug because WooCommerce applies markdown 'after' other filters, which can cause corruption.
                // WooCommerce also applies `wpautop()` twice. Once via filter, then again after a late/buggy markdown.
                if (remove_filter('woocommerce_short_description', 'wc_format_product_short_description', 9999999)) {
                    add_filter('woocommerce_short_description', s::class.'::jetpackMarkdown', -10000);
                } else { // Flag this as a potential problem whenver debugging is enabled by a developer.
                    debug(0, c::issue([], 'Failed to remove `wc_format_product_short_description()` filter.'));
                }
            } // Preservation comes after Jetpack markdown. Markdown should almost always occur first!
            add_filter('woocommerce_short_description', [$this->Utils->Content, 'onTheContentPreserve'], -1000);
            add_filter('woocommerce_short_description', [$this->Utils->Content, 'onTheContentRestore'], 1000);
        }, 100);
    }
}
