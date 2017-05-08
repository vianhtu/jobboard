<?php
/**
 * The Template for displaying actions.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/loop/actions.php.
 *
 * HOWEVER, on occasion JobBoard will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author     FOX
 * @package    JobBoard/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if(empty($actions)){
    return;
}
?>

<div class="loop-actions">
    <?php foreach ($actions as $action): ?>
        <button type="button" class="action action-<?php echo esc_attr($action['id']); ?>" title="<?php echo esc_attr($action['title']); ?>" <?php echo jb_array_to_attributes($action['attribute']); ?>>
            <i class="<?php echo esc_attr($action['icon']); ?>"></i>
        </button>
    <?php endforeach; ?>
</div>
