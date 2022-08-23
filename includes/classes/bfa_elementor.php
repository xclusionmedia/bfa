<?php




class bfa_elementor{

    function __construct(){
        add_action('init', [$this, 'init']);
        add_action('elementor/dynamic_tags/register_tags', [$this,'init_dynamictags'], 9999);
        add_action('elementor/dynamic_tags/register', [$this,'init_dynamictags_category'], 10);

    }
    public static function init(){
        
    }
    
    public static function init_dynamictags(){

        foreach (glob(__DIR__.'/elementor/*.php') as $filename) {
            require_once $filename;
        }
    }
    public static function init_dynamictags_category(){
        \Elementor\Plugin::$instance->dynamic_tags->register_group('gforms', [
            'title' => 'Forms'
        ]);
    }
}
new bfa_elementor();



function elem_main_template($atts) {
    $template_id = esc_attr($atts['id']);
    
    switch_to_blog(1); // your main site ID
    $output = do_shortcode('[elementor-template id="' . $template_id . '"]');
    restore_current_blog();

    return $output;
}
add_shortcode('main_template', 'elem_main_template');