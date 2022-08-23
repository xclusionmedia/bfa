<?php

class bfa_posttypes{
    public function __construct(){

    }

    public static function register_all_post_types(){
        self::register_taxonomy('photo_category', 'Category', 'Categories', ['bfa_photos']);
        self::register_post_type('bfa_photos', 'Photo', 'Photos', ['photo_category'], ['title', 'thumbnail'], 'dashicons-camera-alt');
    }

    public static function register_post_type($slug, $single, $plural, $taxonomies = [], $supports = [], $icon = '', $pos = null) {

        $labels = array(
            'name'                  => _x( $plural, 'Post Type General Name', 'bfa' ),
            'singular_name'         => _x( $single, 'Post Type Singular Name', 'bfa' ),
            'menu_name'             => __( $plural, 'bfa' ),
            'name_admin_bar'        => __( $single, 'bfa' ),
            'archives'              => __( $single.' Archives', 'bfa' ),
            'attributes'            => __( $single.' Attributes', 'bfa' ),
            'parent_item_colon'     => __( 'Parent '.$single.':', 'bfa' ),
            'all_items'             => __( 'All '.$plural, 'bfa' ),
            'add_new_item'          => __( 'Add New '.$single, 'bfa' ),
            'add_new'               => __( 'Add New', 'bfa' ),
            'new_item'              => __( 'New '.$single, 'bfa' ),
            'edit_item'             => __( 'Edit '.$single, 'bfa' ),
            'update_item'           => __( 'Update '.$single, 'bfa' ),
            'view_item'             => __( 'View '.$single, 'bfa' ),
            'view_items'            => __( 'View '.$single, 'bfa' ),
            'search_items'          => __( 'Search '.$single, 'bfa' ),
            'not_found'             => __( 'Not found', 'bfa' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'bfa' ),
            'featured_image'        => __( 'Featured Image', 'bfa' ),
            'set_featured_image'    => __( 'Set featured image', 'bfa' ),
            'remove_featured_image' => __( 'Remove featured image', 'bfa' ),
            'use_featured_image'    => __( 'Use as featured image', 'bfa' ),
            'insert_into_item'      => __( 'Insert into '.$single, 'bfa' ),
            'uploaded_to_this_item' => __( 'Uploaded to this '.$single, 'bfa' ),
            'items_list'            => __( $single.' list', 'bfa' ),
            'items_list_navigation' => __( $single.' list navigation', 'bfa' ),
            'filter_items_list'     => __( 'Filter '.$single.' list', 'bfa' ),
        );
        $args = array(
            'label'                 => __( $single, 'bfa' ),
            'description'           => __( '', 'bfa' ),
            'labels'                => $labels,
            'supports'              => $supports,
            'taxonomies'            => $taxonomies,
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => $pos,
            'menu_icon'             => $icon,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );
        register_post_type( $slug, $args );

    }
    public static function register_taxonomy($slug, $single, $plural, $posttypes = []) {

        $labels = array(
            'name'                       => _x( $plural, 'Taxonomy General Name', 'bfa' ),
            'singular_name'              => _x( $single, 'Taxonomy Singular Name', 'bfa' ),
            'menu_name'                  => __( $single, 'bfa' ),
            'all_items'                  => __( 'All '.$plural, 'bfa' ),
            'parent_item'                => __( 'Parent '.$single, 'bfa' ),
            'parent_item_colon'          => __( 'Parent '.$single.':', 'bfa' ),
            'new_item_name'              => __( 'New '.$single.' Name', 'bfa' ),
            'add_new_item'               => __( 'Add New '.$single, 'bfa' ),
            'edit_item'                  => __( 'Edit '.$single, 'bfa' ),
            'update_item'                => __( 'Update '.$single, 'bfa' ),
            'view_item'                  => __( 'View '.$single, 'bfa' ),
            'separate_items_with_commas' => __( 'Separate '.$plural.' with commas', 'bfa' ),
            'add_or_remove_items'        => __( 'Add or remove '.$plural, 'bfa' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'bfa' ),
            'popular_items'              => __( 'Popular '.$plural, 'bfa' ),
            'search_items'               => __( 'Search '.$plural, 'bfa' ),
            'not_found'                  => __( 'Not Found', 'bfa' ),
            'no_terms'                   => __( 'No '.$plural, 'bfa' ),
            'items_list'                 => __( $plural.' list', 'bfa' ),
            'items_list_navigation'      => __( $plural.' list navigation', 'bfa' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( $slug, $posttypes, $args );

    }
}
new bfa_posttypes();