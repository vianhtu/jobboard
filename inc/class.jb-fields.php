<?php
/**
 * JobBoard Fields.
 *
 * @class 		JobBoard_Fields
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

if (! class_exists('JobBoard_Fields')) :

    class JobBoard_Fields{

        function __construct()
        {
            add_filter('jobboard_field_location_args', array($this, 'field_location'));
        }

        function field_location($field){
            $args       = array(
                'taxonomy'      => $field['taxonomy'],
                'hide_empty'    => false
            );

            $values     = array();
            $parent     = 0;

            if(!empty($field['value'])){
                foreach ($field['value'] as $term_id){
                    $term = get_term($term_id, $field['taxonomy']);
                    if($term && !is_wp_error($term)){
                        $values[$term->parent] = $term->term_id;
                    }
                }
            }

            foreach ($field['options'] as $key => $option){
                if($parent === false) {
                    continue;
                }

                $args['parent'] = $parent;
                $parent = (isset($values[$parent])) ? $values[$parent] : false ;
                $field['options'][$key]['options'] = jb_get_taxonomy_options($args);
            }

            $field['value'] = $values;

            return $field;
        }
    }

endif;