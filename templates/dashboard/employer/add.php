<?php
/**
 * The Template for displaying form add new job.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/employer/new-job.php.
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

<form class="jobboard-form post-form" action="" method="post" enctype="multipart/form-data">

    <?php do_action("jobboard_form_post", $fields); ?>

</form>