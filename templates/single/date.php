<?php
/**
 * The Template for displaying single job date posted.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/single/date.php.
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

<span class="meta-date"><?php esc_html_e('Date : ', 'jobboard'); ?>
    <time class="entry-date updated" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" itemprop="datePosted">
        <?php the_date(); ?>
    </time>
</span>
