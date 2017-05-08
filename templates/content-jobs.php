<?php
/**
 * The template for displaying job content within loops
 *
 * This template can be overridden by copying it to yourtheme/jobboard/content-jobs.php.
 *
 * HOWEVER, on occasion JobBoard will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author  JobBoard
 * @package WooCommerce/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<article id="loop-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php do_action( 'jobboard_loop_item_summary_before' );?>

    <?php do_action( 'jobboard_loop_item_summary' );?>

    <?php do_action( 'jobboard_loop_item_summary_after' );?>

</article>