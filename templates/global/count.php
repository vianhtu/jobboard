<?php
/**
 * The Template for displaying jobboard count.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/global/count.php.
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
    exit;
}
?>

<span class="jobboard-count"><?php echo esc_html($count); ?></span>
