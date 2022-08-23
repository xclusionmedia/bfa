<?php

class bfa_forms
{

    public function __construct()
    {
        add_action('saved_photo_category', [$this, 'dynamic_upload_fields'], 10, 3);

        add_filter('gform_form_settings_fields', [$this, 'add_gravityform_options'], 10, 2);

        add_filter('gform_pre_render', [$this, 'set_pricing'], 10, 1);
        add_action('gform_post_submission', [$this, 'process_images'], 10, 2);
        add_filter('gform_field_value_photo_prod_qty', [$this, 'change_product_qty'], 10, 3); // <- doesnt work.
        add_filter('gform_get_field_value', [$this, 'all_field_values'], 10, 3); // <- doesnt work either.
        
    }
    public function all_field_values($value, $lead, $field){
        $form = GFAPI::get_form($field['formId']);
        if (!rgar($form, 'bfa_generate_uploadfields')) {
            error_log("Price changes only apply to the upload form ");
            return $value;
        }
        if($field->type == 'product'){

            $upload_count = self::count_uploaded_files();

            $value[$field->id.'.1'] = "The label";
            $value[$field->id.'.2'] = self::get_product_price();
            $value[$field->id.'.3'] = $upload_count;

            return $value;
        }
        return $value;
    }
    public static function change_product_qty($value, $field, $name)
    {
        $form = GFAPI::get_form($field['formId']);
        if (!rgar($form, 'bfa_generate_uploadfields')) {
            error_log("Price changes only apply to the main upload form ");
            return $value;
        }

        $upload_count = self::count_uploaded_files();
        return $upload_count;

    }
    public static function get_product_price()
    {
        $upload_count = self::count_uploaded_files();

        $vbp = get_field('volume_based_pricing', 'option');
        $default = floatval(22.5);
        if ($vbp) {
            foreach ($vbp as $pricerange) {
                $qty_from = $pricerange['qty_from'];
                $qty_to = $pricerange['qty_to'];
                if ($upload_count >= $qty_from && $upload_count <= $qty_to) {
                    return floatval($pricerange['price_per_photo']);
                }
            }
        } else {
            return $default;
        }
        return $default;
    }

    public static function add_gravityform_options($fields, $form)
    {
        $fields['bfa_options'] = array(
            'title' => esc_html__('BFA Options', 'bfa'),
            'fields' => array(
                array(
                    'name' => 'bfa_generate_uploadfields',
                    'type' => 'toggle',
                    'label' => __('Generate upload fields and proces pricing fields', 'bfa'),
                ),
            ),
        );
        return $fields;
    }

    public static function dynamic_upload_fields($term_id, $tt_id, $update)
    {
        $all_forms = GFAPI::get_forms(true);
        foreach ($all_forms as $form) {
            if (!rgar($form, 'bfa_generate_uploadfields')) {
                error_log("Not generating new fields for this form " . $form['id']);
                continue;
            }

            /* find form section*/
            $upload_fields = [];
            $upload_fields_start = 0;
            $upload_fields_end = 0;
            foreach ($form['fields'] as $key => $field) {
                if ('%uploads_start%' == $field['label']) {
                    $upload_fields_start = $key;
                }

                if ('%uploads_end%' == $field['label']) {
                    $upload_fields_end = $key;
                }

            }

            if (0 == $upload_fields_start && 0 == $upload_fields_end) {
                // Form does not have upload start/stop sections yet.
                $upload_fields_start = count($form['fields']);
                $form['fields'][] = GF_Fields::create(array(
                    'type' => 'section',
                    'id' => GFFormsModel::get_next_field_id($form['fields']),
                    'formId' => $form['id'],
                    'required' => false,
                    'label' => '%uploads_start%',
                    'visibility' => 'hidden',
                    'pageNumber' => 1,
                ));
                $upload_fields_end = count($form['fields']);
                $form['fields'][] = GF_Fields::create(array(
                    'type' => 'section',
                    'id' => GFFormsModel::get_next_field_id($form['fields']) + 1,
                    'formId' => $form['id'],
                    'required' => false,
                    'label' => '%uploads_end%',
                    'visibility' => 'hidden',
                    'pageNumber' => 1,
                ));
            } else {
                /* remove any upload fields already present */
                for ($x = $upload_fields_start + 1; $x < $upload_fields_end; $x++) {
                    unset($form['fields'][$x]);
                }
            }

            $args = [
                'taxonomy' => 'photo_category',
                'hide_empty' => false,
            ];
            $categories = get_terms($args);

            $pos = $upload_fields_start + 1;
            error_log('Creating new fields');
            foreach ($categories as $category) {
                $form = self::create_title_section($form, $category->name, $form['fields'][$upload_fields_start]['pageNumber'], $pos);
                $pos++;
                $form = self::create_upload_fieldset($form, $form['fields'][$upload_fields_start]['pageNumber'], $pos, $category->name);
                $pos += 5;
            }
            GFAPI::update_form($form);

        }

        return;

    }

    public static function create_title_section($form, $content, $pageNumber, $pos)
    {
        $new_fields = [];
        $new_fields[] = GF_Fields::create(array(
            'type' => 'section',
            'id' => GFFormsModel::get_next_field_id($form['fields']),
            'formId' => $form['id'],
            'isRequired' => false,
            'label' => $content,
            'content' => "<h3>" . $content . "</h3>",
            'pageNumber' => $pageNumber,
            'bfa_identifier' => 'header_' . strtolower($content),

        ));
        self::array_insert($form['fields'], $pos, $new_fields);
        return $form;
    }
    public static function create_upload_fieldset($form, $pageNumber, $pos, $category)
    {
        $num = 5;
        $new_fields = [];
        for ($i = 0; $i < $num; $i++) {
            $readable_key = (string)($i + 1);
            $new_fields[] = GF_Fields::create(array(
                'type' => 'fileupload',
                'id' => GFFormsModel::get_next_field_id($form['fields']) + $i,
                'formId' => $form['id'],
                'isRequired' => false,
                'label' => sprintf(__("Photo %s", "bfa"), $readable_key),
                'pageNumber' => $pageNumber,
                'bfa_identifier' => 'photo_' . strtolower($category) . '_' . $readable_key,
                'size' => 'large',
                "useRichTextEditor" => false,
                'layoutGridColumnSpan' => 12,
                'errors' => [],
                'calculationFormula' => "",
                'calculationRounding' => "",
                'enableCalculation' => "",
                'enableEnhancedUI' => false,
                'displayOnly' => false,
                'inputs' => null,
                'productField' => ""

            ));
        }
        self::array_insert($form['fields'], $pos, $new_fields);
        return $form;

    }
    public static function count_uploaded_files()
    {
        $upload_count = 0;
        foreach ($_FILES as $file) {
            if ("" !== $file['tmp_name']) {
                $upload_count++;
            }
        }

        if (isset($_POST['gform_uploaded_files'])) {
            $uploaded_files = GFCommon::json_decode(stripslashes(GFForms::post('gform_uploaded_files')));
            if (!is_array($uploaded_files)) {
                $uploaded_files = array();
            }
            $upload_count += count($uploaded_files);
        }
        return $upload_count;
    }

    public function set_pricing($form)
    {
        return $form; // this doesn't seem the correct filter
        
        if (!rgar($form, 'bfa_generate_uploadfields')) {
            return $form;
        }

        // Loop possible uploaded files for pricing
        $upload_count = self::count_uploaded_files();



        return $form;
    }

    public static function process_images($entry, $form)
    {
        // This will process the uploaded images later on
        return $entry;
    }
    public static function array_insert(&$array, $position, $insert)
    {
        if (is_int($position)) {
            array_splice($array, $position, 0, $insert);
        } else {
            $pos = array_search($position, array_keys($array));
            $array = array_merge(
                array_slice($array, 0, $pos),
                $insert,
                array_slice($array, $pos)
            );
        }
    }
}
new bfa_forms();
