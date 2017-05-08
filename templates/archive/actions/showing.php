<?php
/**
 * The Template for displaying job archive action showing.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/archive/actions/showing.php.
 *
 * HOWEVER, on occasion JobBoard will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		FOX
 * @package 	JobBoard/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<span class="archive-showing"><?php printf(esc_html__('Showing %s-%s of %s results', 'jobboard'), $showing['paged'], $showing['current'], $showing['all'] ); ?></span>
