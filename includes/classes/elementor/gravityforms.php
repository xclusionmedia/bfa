<?php
use ElementorPro\Modules\DynamicTags\Tags\Base\Tag;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Embed;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Repeater;
use Elementor\Utils;
use ElementorPro\Modules\DynamicTags\Module;
use ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag;
use ElementorPro\Plugin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class bfa_tag_gravityform extends \Elementor\Core\DynamicTags\Tag {

    public function get_name()
    {
        return 'bfa_tag_gravityform';
    }

    public function get_categories()
    {
        return [Module::TEXT_CATEGORY, Module::BASE_GROUP, MODULE::POST_META_CATEGORY];
    }

    public function get_group()
    {
        return ['gforms'];
    }
    protected function register_controls()
    {
        if(!class_exists('RGFormsModel')) return;

        $allforms = RGFormsModel::get_forms();
        $activeforms = [];
        foreach ($allforms as $form) {
            if($form ->is_active){
                $activeforms[$form->id] = $form->title;
            }
        }

        $this->add_control(
            'form_id',
            [
                'label' => __('Form', 'elementor-pro'),
                'type' => Controls_Manager::SELECT,
                'options' => $activeforms,
            ]
        );
    }
    public function get_title()
    {
        return __('Gravity form', 'bfa');
    }

    public function render()
    {
        $settings = $this->get_settings();
        if(!isset($settings['form_id'])) {
            echo "Select a form.";
            return;
        }
        
        echo do_shortcode('[gravityform id="'.$settings['form_id'].'" title="false" description="false" ajax="true"]');
    }
}
\Elementor\Plugin::$instance->dynamic_tags->register_tag('bfa_tag_gravityform');
