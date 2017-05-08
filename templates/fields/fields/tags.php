<?php
/**
 * The Template for displaying input tags.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/fields/fields/tags.php.
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

<select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" class="<?php echo esc_attr($type); ?>" data-placeholder="<?php echo esc_attr($placeholder); ?>" multiple>

    <?php if(!empty($value)): ?>
        <?php foreach ($value as $v): ?>

        <option value="<?php echo esc_attr($v); ?>" selected><?php echo esc_html($v); ?></option>

        <?php endforeach; ?>
    <?php endif; ?>

</select>
