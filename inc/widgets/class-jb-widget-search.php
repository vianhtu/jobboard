<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Job Search Widget.
 *
 * @author   JobBoard
 * @category Widgets
 * @package  JobBoard/Widgets
 * @version  1.0.0
 * @extends  JB_Widget
 */
class JB_Widget_Search extends JB_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->widget_cssclass    = 'jobboard-widget widget-search';
        $this->widget_description = esc_html__( 'A Search box for JobBoard only.', 'jobboard' );
        $this->widget_id          = 'jobboard-widget-search';
        $this->widget_name        = esc_html__( 'JobBoard Search', 'jobboard' );
        $this->settings           = array(
            'title'  => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Title', 'jobboard' )
            )
        );

        parent::__construct();
    }

    /**
     * Output widget.
     *
     * @see WP_Widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {

        $this->widget_start( $args, $instance );

        echo '<div class="widget-content">';

        jb_template_search_form();

        echo '</div>';

        $this->widget_end( $args );
    }
}
