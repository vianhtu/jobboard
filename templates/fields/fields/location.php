<?php
/**
 * The Template for displaying select location.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/fields/fields/location.php.
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

<div class="row field-<?php echo esc_attr($type); ?>" data-taxonomy="<?php echo esc_attr($taxonomy); ?>">

    <?php foreach ($options as $key => $option): ?>

    <div class="col-xs-12 col-sm-12 col-md-4 location-<?php echo esc_attr($option['id']); ?>">
        <select id="<?php echo esc_attr(sprintf('%s-%s', $id, $option['id'])) ?>" name="<?php echo esc_attr(sprintf('%s[%s]', $id, $option['id'])); ?>" class="select" data-level="<?php echo esc_attr($key);?>" data-placeholder="<?php echo esc_attr($option['placeholder']); ?>">
            <option value></option>

            <?php if(!empty($option['options'])): foreach ($option['options'] as $_key => $_name): ?>

                <option value="<?php echo esc_attr($_key); ?>"<?php jb_selected($value, $_key); ?>><?php echo esc_html($_name); ?></option>

            <?php endforeach; endif;?>

        </select>
    </div>

    <?php endforeach; ?>

</div>
