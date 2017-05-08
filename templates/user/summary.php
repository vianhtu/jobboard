<?php
/**
 * The Template for displaying user summary.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/user/summary.php.
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

<div class="jobboard-user-summary"<?php if($cover_image = jb_account_get_cover_image()){ echo ' style="background-image: url('.esc_url($cover_image).')"'; } ?>>
    <div class="row">
        <div class="col-xs-12 col-md-8">
            <div class="summary-title">
                <span><?php jb_account_the_type(); ?></span>
                <h1><?php jb_account_the_display_name(); ?></h1>
            </div>
            <div class="summary-meta">
                <ul>
                <?php foreach ($summaries as $key => $val ): ?>
                    <?php if(!$key) { continue; } ?>
                    <?php if(is_array($val)): $key_before = sprintf($val['before'], $key); $key_after = sprintf($val['after'], $key); ?>
                        <li><?php echo sprintf($val['title'], $key_before.  $key . $key_after); ?></li>
                    <?php else: ?>
                        <li><?php echo sprintf($val, $key); ?></li>
                    <?php endif; ?>
                <?php endforeach;?>
                </ul>
            </div>
        </div>
        <div class="col-xs-12 col-md-4">
            <?php do_action('jobboard_user_' . jb_account_get_type(true) . '_summary_actions'); ?>
        </div>
    </div>
</div>
