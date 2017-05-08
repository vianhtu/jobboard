<?php
/**
 * The Template for displaying widgets specialism filters.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/widgets/widget-specialism-filters.php.
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

if(empty($terms) || is_wp_error($terms)){
    return;
}

$terms_count = count($terms);
?>

<div class="widget-content">
    <ul>

        <?php foreach ($terms as $k => $term): ?>

        <?php $checked = in_array($term->term_id, $values) ? 'checked="checked"' : '' ;?>

        <li>

            <input id="specialism-filter-<?php echo esc_attr($term->term_id); ?>" type="checkbox" name="specialism-filters[]" value="<?php echo esc_attr($term->term_id); ?>" <?php echo $checked; ?>/>

            <label for="specialism-filter-<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></label>

            <?php if ($count): ?>

                <span class="count">(<?php echo esc_html($term->count); ?>)</span>

            <?php endif; ?>

            </li>

            <?php if($k == 6 && $terms_count > 7): ?>

            <li>

                <a class="specialism-filter-more md-trigger" data-modal="specialism-filter-modal" href="javascript:void(0)"><?php echo esc_html__('More', 'jobboard'); ?></a>

                <?php jb_get_template('modal/modal-start.php', array('modal' => 'specialism-filter-modal')); ?>

                <ul class="specialism-filter-extra">

            <?php endif;?>

        <?php endforeach;?>

        <?php if($terms_count > 7): ?>

            </ul>

            <?php jb_get_template('modal/modal-end.php'); ?>

            </li>

        <?php endif; ?>

    </ul>
</div>
