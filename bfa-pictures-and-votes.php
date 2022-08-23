<?php

/**
 * Plugin Name:       BFA - Pictures and votes
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Deze plugin bevat de functionaliteit voor de foto module tbv Bruidsfoto awards
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Haiko Gerkema @ Impression
 * Author URI:        https://impression.nl
 * Text Domain:       bfa
 * Domain Path:       /languages
 */

if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

register_activation_hook(__FILE__, array('XM_RealWorksConnector', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('XM_RealWorksConnector', 'plugin_deactivation'));

define('BFA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BFA_PLUGIN_URL', plugin_dir_url(__FILE__));

class bfa_pav
{
    public $file_base;
    public $plugin_dir;

    public static function get_instance()
    {

        static $plugin;
        if (!isset($plugin)) {
            $plugin = new bfa_pav();
        }
        return $plugin;
    }

    public function __construct()
    {
        $this->file_base = plugin_dir_path(dirname(__FILE__)) . 'bfa-pictures-and-votes.php';
        $this->plugin_dir = plugin_dir_path(__FILE__);

        /* fetch all classes */
        foreach (glob($this->plugin_dir . 'includes/classes/*.php') as $filename) {
            require_once $filename;
        }

        $this->init();
    }

    private function init()
    {
        add_action('init', array('bfa_posttypes', 'register_all_post_types'), 49);
    }

    public function plugin_activate()
    {
    }
    public function plugin_deactivate()
    {
    }
}
$bfa_pav = bfa_pav::get_instance();

// runs filter once
if (!function_exists('add_filter_once')) {
    function add_filter_once($hook, $callback, $priority = 10, $params = 1)
    {
        add_filter($hook, function ($first_arg) use ($callback) {
            static $ran = false;
            if ($ran) {
                return $first_arg;
            }
            $ran = true;
            return call_user_func_array($callback, func_get_args());
        }, $priority, $params);
    }
}




add_filter('enable_wp_debug_mode_checks', function (){
    error_reporting( E_ALL );
    ini_set( 'display_errors', 0 );
    $log_path = WP_CONTENT_DIR . '/debug.log';
    ini_set( 'log_errors', 1 );
    ini_set( 'error_log', $log_path );

    if ( defined( 'XMLRPC_REQUEST' ) || defined( 'REST_REQUEST' ) || ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) || wp_doing_ajax() || wp_is_json_request() ) {
		ini_set( 'display_errors', 1);
    }
    
    return true;
});