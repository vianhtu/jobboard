<?php
/**
 * The Template for displaying alert messages.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/notices/alert.php.
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

<div class="jobboard-alert">
    <div class="alert-content"></div>
    <div class="alert-actions" style="display: none;">
        <button id="actions-yes" type="button" class="button" value="1"><?php esc_html_e('Yes', 'jobboard'); ?></button>
        <button id="actions-no" type="button" class="button" value="0"><?php esc_html_e('No', 'jobboard'); ?></button>
    </div>
</div>
