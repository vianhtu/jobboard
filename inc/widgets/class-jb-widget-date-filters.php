<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Job Date Filters Widget.
 *
 * @author   JobBoard
 * @category Widgets
 * @package  JobBoard/Widgets
 * @version  1.0.0
 * @extends  JB_Widget
 */
class JB_Widget_Date_Filters extends JB_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->widget_cssclass    = 'jobboard-widget widget-date-filters';
        $this->widget_description = esc_html__( 'A date filters box for JobBoard only.', 'jobboard' );
        $this->widget_id          = 'jobboard-widget-date-filters';
        $this->widget_name        = esc_html__( 'JobBoard Date Filters', 'jobboard' );
        $this->settings           = array(
            'title'  => array(
                'type'  => 'text',
                'std'   => esc_html__( 'Date Posted', 'jobboard' ),
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

        $value = isset($_GET['date-filters']) ? $_GET['date-filters'] : '';

        $times = apply_filters('jb/widget/filters/times', array(
            6   => esc_html__('Last 6 Hours',   'jobboard'),
            12  => esc_html__('Last 12 Hours',  'jobboard'),
            24  => esc_html__('Last 24 Hours',  'jobboard'),
            168 => esc_html__('Last 7 Days',    'jobboard'),
            720 => esc_html__('Last 30 Days',   'jobboard'),
            0   => esc_html__('All',            'jobboard'),
        ));


        $this->widget_start( $args, $instance );

        jb_get_template('widgets/widget-date-filters.php', array('times' => $times, 'value' => $value));

        $this->widget_end( $args );
    }
}
