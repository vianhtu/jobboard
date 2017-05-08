<?php
/**
 * The Template for displaying user profile.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/user.php.
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

<?php get_header('jobboard'); ?>

<?php do_action( 'jobboard_user_before_content' ); ?>

<?php do_action( 'jobboard_user_before_' . jb_account_get_type(true) . '_content'); ?>

<div <?php jb_account_the_user_class(); ?>>
    <div class="row">
        <div class="col-xs-12 col-md-8">
            <?php do_action('jobboard_user_' . jb_account_get_type(true) . '_content'); ?>
        </div>
        <div class="col-xs-12 col-md-4">
            <?php do_action('jobboard_user_' . jb_account_get_type(true) . '_content_actions'); ?>
        </div>
    </div>
</div>

<?php do_action( 'jobboard_user_after_content' ); ?>

<?php do_action( 'jobboard_sidebar_user' ); ?>

<?php get_footer('jobboard'); ?>
