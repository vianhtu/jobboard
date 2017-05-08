<?php
/**
 * The Template for displaying dashboard navigation.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/global/navigation.php.
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

<ul class="navigations">

    <?php foreach ($navigation as $endpoint): ?>

        <li class="<?php echo jb_account_navigation_class($endpoint['endpoint'], array('nav-' . $endpoint['id'])); ?>">
            <a class="title" href="<?php echo esc_url( jb_page_endpoint_url( $endpoint['endpoint'], $permalink ) ); ?>">
                <?php echo apply_filters("jobboard_dashboard_navigation_{$endpoint['id']}_title", $endpoint['title'], $endpoint['endpoint']); ?>
            </a>
        </li>

    <?php endforeach; ?>

</ul>
