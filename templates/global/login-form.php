<?php
/**
 * The Template for displaying login form.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/global/login-form.php.
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

<form id="<?php echo esc_attr($args['form_id']); ?>" class="jobboard-form login-form" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">

    <?php do_action('jobboard_form_login_before'); ?>

    <p class="login-username">
        <label for="<?php echo esc_attr($args['form_id']) . '-log'; ?>"><?php echo esc_html($args['label_username']); ?></label>
        <input type="text" name="log" id="<?php echo esc_attr($args['form_id']) . '-log'; ?>" class="input" value="" size="20" />
    </p>

    <p class="login-password">
        <label for="<?php echo esc_attr($args['form_id']) . '-pwd'; ?>"><?php echo esc_html($args['label_password']); ?></label>
        <input type="password" name="pwd" id="<?php echo esc_attr($args['form_id']) . '-pwd'; ?>" class="input" value="" size="20" />
    </p>

    <p class="login-remember">
        <label>
            <input name="rememberme" type="checkbox" id="<?php echo esc_attr($args['form_id']) . '-rememberme'; ?>" value="forever"/>
            <?php echo esc_html($args['label_remember']); ?>
        </label>
    </p>

    <p class="login-submit">
        <input type="submit" name="wp-submit" id="<?php echo esc_attr($args['form_id']) . '-submit'; ?>" class="button button-primary" value="<?php echo esc_attr($args['label_log_in']); ?>" />
        <input type="hidden" name="redirect_to" value="<?php echo esc_url($args['redirect_to']); ?>" />
        <input type="hidden" name="dashboard" value="<?php echo esc_attr($args['dashboard']); ?>">
    </p>

    <?php do_action('jobboard_form_login_after'); ?>
</form>
