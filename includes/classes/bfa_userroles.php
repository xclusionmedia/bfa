<?php

register_activation_hook( __FILE__, array( 'bfa_userroles', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'bfa_userroles', 'plugin_deactivation' ) );

class bfa_userroles
{
    public function __construct()
    {
        add_filter('show_admin_bar', [$this, 'set_admin_bar_permissions']);
        add_action('admin_init', [$this, 'set_wpadmin_permissions']);
        $this->maybe_add_roles();

    }
    public function plugin_activate()
    {
        bfa_userroles::add();
    }
    public function plugin_deactivate()
    {
        bfa_userroles::remove();
    }
    public function add()
    {
        /* maybe add capabilities */
        $add_capabilities = [];

        if (get_role('subscriber')) {
            $shopmanagercaps = get_role('subscriber')->capabilities;
            $newcapabilities = array_merge($add_capabilities, $shopmanagercaps);
        } else {
            $newcapabilities = $add_capabilities;
        }

        $wp_roles = new WP_Roles();
        $wp_roles->remove_role("jury_expert");
        $wp_roles->add_role(
            'jury_expert',
            __('Jury expert', 'bfa'),
            $newcapabilities
        );

        $wp_roles->remove_role("photographer");
        $wp_roles->add_role(
            'photographer',
            __('Photographer', 'bfa'),
            $newcapabilities
        );
    }
    public static function maybe_add_roles()
    {
        if (!get_role('jury_expert') || !get_role('photographer')) {
            self::add();
        }
    }
    public static function remove()
    {
        remove_role('jury_expert');
        remove_role('photographer');
    }

    public static function user_is($role)
    {
        $user = wp_get_current_user();
        $roles = (array)$user->roles;

        if (in_array($role, $roles)) {
            return true;
        } else {
            return false;
        }
    }
    public static function name()
    {
        $user = wp_get_current_user();
        return $user->user_login;
    }

    public static function get_user()
    {
        return wp_get_current_user();
    }

    public static function set_admin_bar_permissions($show)
    {
        if (!current_user_can('administrator')) {
            return false;
        }

        return $show;
    }
    public static function set_wpadmin_permissions()
    {
        if (is_admin() && !current_user_can('administrator') && !(defined('DOING_AJAX') && DOING_AJAX)) {
            wp_safe_redirect(home_url());
            exit;
        }
    }

}
new bfa_userroles();
