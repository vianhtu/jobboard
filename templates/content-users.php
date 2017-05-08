<?php
/**
 * The template for displaying job content in the users.php template.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/content-users.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
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

<article id="loop-<?php jb_account_the_id(); ?>" <?php jb_account_the_user_class(); ?>>

    <?php do_action( 'jobboard_users_loop_summary_before' );?>

    <?php do_action( 'jobboard_users_loop_' . jb_account_get_type(true) . '_summary' );?>

    <?php do_action( 'jobboard_users_loop_summary_after' );?>

</article>