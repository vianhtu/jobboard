<?php
/**
 * The Template for displaying before fields.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/fields/field-before.php.
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

<div class="<?php echo esc_attr($class); ?>">
    <div class="field-header">
        <label class="field-name" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label>
        <?php if(!empty($subtitle)): ?>
            <span class="field-subtitle"><?php echo $subtitle; ?></span>
        <?php endif; ?>
    </div>
    <div class="field-content">
