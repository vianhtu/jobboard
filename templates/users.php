<?php
/**
 * The Template for displaying users listing.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/users.php.
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

<?php do_action( 'jobboard_users_before_content' ); ?>

<?php if(jb_account_get_users()): ?>

    <?php do_action( 'jobboard_users_loop_before' ); ?>

    <?php foreach (jb_account_get_users() as $user): jb_account_the_user($user->data); ?>

        <?php jb_get_template_part( 'content', 'users' ); ?>

    <?php endforeach; ?>

    <?php do_action( 'jobboard_users_loop_after' ); ?>

<?php else: ?>
    <?php jb_get_template_part( 'users/not-found' ); ?>
<?php endif; ?>

<?php do_action( 'jobboard_users_after_content' ); ?>

<?php do_action( 'jobboard_sidebar_users' ); ?>

<?php get_footer('jobboard'); ?>
