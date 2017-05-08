<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Specialisms Filters Widget.
 *
 * @author   JobBoard
 * @category Widgets
 * @package  JobBoard/Widgets
 * @version  1.0.0
 * @extends  JB_Widget
 */
class JB_Widget_Specialism_Filters extends JB_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobboard-widget widget-specialism-filters';
		$this->widget_description = esc_html__( 'A Specialism filters box for JobBoard only.', 'jobboard' );
		$this->widget_id          = 'jobboard-widget-specialism-filters';
		$this->widget_name        = esc_html__( 'JobBoard Specialism Filters', 'jobboard' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Sectors', 'jobboard' ),
				'label' => esc_html__( 'Title', 'jobboard' )
			),
			'orderby' => array(
				'type'  => 'select',
				'std'   => 'name',
				'label' => esc_html__( 'Order by', 'jobboard' ),
				'options' => array(
					'order' => esc_html__( 'Category Order', 'jobboard' ),
					'name'  => esc_html__( 'Name', 'jobboard' ),
				)
			),
			'count' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => esc_html__( 'Show job counts', 'jobboard' )
			),
            'parent' => array(
                'type'  => 'checkbox',
                'std'   => 1,
                'label' => esc_html__( 'Hide children', 'jobboard' )
            ),
			'hide_empty' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => esc_html__( 'Hide empty', 'jobboard' )
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

        $values             = isset($_GET['specialism-filters']) ? $_GET['specialism-filters'] : array();
		$count              = isset( $instance['count'] ) ? $instance['count'] : $this->settings['count']['std'];
		$hide_empty         = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];
        $list_args          = array( 'taxonomy' => 'jobboard-tax-specialisms', 'hide_empty' => $hide_empty);

        if(isset( $instance['parent'] ) && $instance['parent']){
            $list_args['parent'] = 0;
        }

		$terms              = get_terms($list_args);

		$this->widget_start( $args, $instance );

        jb_get_template('widgets/widget-specialism-filters.php', array('terms' => $terms, 'count' => $count, 'values' => $values));

		$this->widget_end( $args );
	}
}
