<?php
/**
 * The Template for displaying account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/account.php.
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

<div class="dashboard-navigations col-xs-12 col-md-3">

    <?php do_action("jobboard_dashboard_".jb_account_type()."_navigation"); ?>

</div>
<div class="dashboard-content col-xs-12 col-md-9">

    <?php do_action("jobboard_dashboard_".jb_account_type()."_content"); ?>

</div>
