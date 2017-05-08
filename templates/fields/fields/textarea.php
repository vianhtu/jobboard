<?php
/**
 * The Template for displaying textarea.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/fields/fields/textarea.php.
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

<textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" class="<?php echo esc_attr($type); ?>" placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo wp_kses_post($value); ?></textarea>