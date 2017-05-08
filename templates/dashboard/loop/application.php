<?php
/**
 * The Template for displaying application.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/loop/application.php.
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
?>

<div class="loop-application">
    <a href="javascript:void(0)" class="application md-trigger" data-modal="applications" data-id="<?php the_ID(); ?>">
        <?php echo sprintf(esc_html__('View Applications [%d]', 'jobboard'), $number); ?>
    </a>
</div>