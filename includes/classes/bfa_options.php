<?php

// Check if ACF is used with another plugin, if not already called, use this one
if (!class_exists('acf')) {
    // Load only limited functionality
    define('ACF_LITE', false);

    // Load the ACF Core
    include_once BFA_PLUGIN_PATH . 'acf/acf.php';

}

// Repeater Add-On
if (!class_exists('acf_repeater_plugin')) {
    include_once BFA_PLUGIN_PATH . 'acf/add-ons/acf-repeater/acf-repeater.php';
}

class bfa_options
{
    public function __construct()
    {
        add_filter('acf/settings/save_json', [$this, 'bfa_acf_json_save_point'], 99);
        add_filter('acf/settings/load_json', [$this, 'bfa_acf_json_load_point'], 99);
        add_filter('acf/settings/l10n', [$this, 'bfa_acf_settings_localization']);
        add_filter('acf/settings/l10n_textdomain', [$this, 'bfa_acf_settings_textdomain']);
        add_action('acf/init', [$this, 'bfa_options_init']);

    }
    public function bfa_options_init()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(array(
                'page_title' => __('Checkout', 'bfa'),
                'menu_title' => __('Checkout settings', 'bfa'),
            ));
        }
    }

    public function bfa_acf_json_save_point($path)
    {
        // append path
        $paths = BFA_PLUGIN_PATH . 'acf/json/';

        // return
        return $paths;
    }

    public function bfa_acf_json_load_point($paths)
    {

        $paths[] = BFA_PLUGIN_PATH . 'acf/json/';

        // return
        return $paths;
    }

    public function bfa_acf_settings_localization($localization)
    {
        return true;
    }

    public function bfa_acf_settings_textdomain($domain)
    {
        return 'bfa';
    }

}
new bfa_options();