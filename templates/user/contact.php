<?php
/**
 * The Template for displaying user contact.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/user/contact.php.
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

<div class="user-contact">
    <h3><?php echo sprintf(esc_html__( 'Contact %s%s%s', 'jobboard' ), '<span>', jb_account_get_display_name(), '</span>'); ?></h3>
    <div class="contact-form">
        <form class="jobboard-form message-form" method="POST">
            <div class="form-fields">
                <input type="text" name="contact-name" class="field-text" value="<?php jb_account_the_current_name(); ?>" placeholder="<?php esc_html_e('Name *', 'jobboard'); ?>">
                <input type="email" name="contact-email" class="field-text" value="<?php jb_account_the_current_email(); ?>" placeholder="<?php esc_html_e('Email *', 'jobboard'); ?>">
                <textarea name="contact-message" class="field-textarea" placeholder="<?php esc_html_e('Your Message *', 'jobboard'); ?>"></textarea>
            </div>
            <div class="form-actions message-actions">
                <?php wp_nonce_field( 'send_message' ); ?>
                <input type="submit" class="button" value="<?php esc_html_e('Send Message', 'jobboard'); ?>">
                <input type="hidden" name="id" value="<?php jb_account_the_id(); ?>">
                <input type="hidden" name="action" value="send_message">
                <input type="hidden" name="form" value="jobboard-form">
            </div>
        </form>
    </div>
    <div class="contact-address">
        <h4><?php esc_html_e('Address', 'jobboard') ?></h4>
        <span><?php jb_account_the_location(); ?></span>
    </div>
    <div class="contact-email">
        <h4><?php esc_html_e('Email', 'jobboard') ?></h4>
        <a href="mailto:<?php jb_account_the_email(); ?>"><?php jb_account_the_email(); ?></a>
    </div>
    <div class="contact-tel">
        <h4><?php esc_html_e('Call', 'jobboard') ?></h4>
        <a href="tel:<?php echo preg_replace('/[^0-9+-]/', '', jb_account_get_phone()); ?>"><?php jb_account_the_phone() ?></a>
    </div>
</div>