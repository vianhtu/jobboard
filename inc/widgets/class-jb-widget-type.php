<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Job Type Widget.
 *
 * @author   JobBoard
 * @category Widgets
 * @package  JobBoard/Widgets
 * @version  1.0.0
 * @extends  JB_Widget
 */
class JB_Widget_Type extends JB_Widget {

	/**
	 * Category ancestors.
	 *
	 * @var array
	 */
	public $cat_ancestors;

	/**
	 * Current Category.
	 *
	 * @var bool
	 */
	public $current_cat;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobboard-widget widget-types';
		$this->widget_description = esc_html__( 'A list of job types.', 'jobboard' );
		$this->widget_id          = 'jobboard-widget-types';
		$this->widget_name        = esc_html__( 'JobBoard Job Types', 'jobboard' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Vacancy Type', 'jobboard' ),
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
            'color' => array(
                'type'  => 'checkbox',
                'std'   => 1,
                'label' => esc_html__( 'Show type color', 'jobboard' )
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
		global $wp_query;

		$count              = isset( $instance['count'] ) ? $instance['count'] : $this->settings['count']['std'];
		$orderby            = isset( $instance['orderby'] ) ? $instance['orderby'] : $this->settings['orderby']['std'];
		$hide_empty         = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];
		$color              = isset( $instance['color'] ) ? $instance['color'] : $this->settings['color']['std'] ;
        $list_args          = array( 'show_count' => $count, 'show_color' => $color, 'taxonomy' => 'jobboard-tax-types', 'hide_empty' => $hide_empty );

		// Menu Order
		$list_args['menu_order'] = false;
		if ( $orderby == 'order' ) {
			$list_args['menu_order'] = 'asc';
		} else {
			$list_args['orderby']    = 'title';
		}

		// Setup Current Category
		$this->current_cat   = false;
		$this->cat_ancestors = array();

		if ( is_jb_type() ) {

			$this->current_cat   = $wp_query->queried_object;
			$this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'jobboard-tax-types' );
		}

		$this->widget_start( $args, $instance );

        include_once( JB()->plugin_directory . 'inc/walkers/class-jb-cat-list-walker.php' );

        $list_args['walker']                     = new JB_Cat_List_Walker;
        $list_args['title_li']                   = '';
        $list_args['pad_counts']                 = 1;
        $list_args['show_option_none']           = esc_html__('Type does not exists.', 'jobboard' );
        $list_args['current_category']           = ( $this->current_cat ) ? $this->current_cat->term_id : '';
        $list_args['current_category_ancestors'] = $this->cat_ancestors;

        echo '<div class="widget-content">';
        echo '<ul>';

        wp_list_categories( apply_filters( 'jb/widget/type/args', $list_args ) );

        echo '</ul>';
        echo '</div>';

		$this->widget_end( $args );
	}
}
