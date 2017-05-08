<?php
/**
 * The Template for displaying applications.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/employer/jobs-applications.php.
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

<?php foreach ($applications as $key => $application): ?>

    <tr>
        <td class="column-candidates column-0" data-colname="<?php esc_html_e('Applications', 'jobboard'); ?>">
            <div class="loop-name">
                <strong>
                    <a href="<?php echo esc_url(jb_account_get_permalink($application->ID)); ?>" class="user-name" target="_blank">
                        <?php echo esc_html($application->display_name); ?>
                    </a>
                </strong>
            </div>
            <div class="loop-email">
                <small>
                    <a href="mailto:<?php echo esc_attr($application->user_email); ?>" class="user-email">
                        <?php echo esc_html($application->user_email); ?>
                    </a>
                </small>
            </div>
            <button type="button" class="toggle-row">
                <span class="screen-reader-text"><?php esc_html_e('More details', 'jobboard'); ?></span>
            </button>
        </td>
        <td class="column-cv column-1" data-colname="<?php esc_html_e('Download CV', 'jobboard'); ?>">
            <div class="loop-download">
                <a href="<?php echo esc_url(jb_candidate_get_cv_url($application->ID)); ?>" class="download-cv" title="<?php esc_attr_e('Download', 'jobboard'); ?>" target="_blank">
                    <i class="fa fa-download"></i>
                </a>
            </div>
        </td>
        <td class="column-status column-2" data-colname="<?php esc_html_e('Status', 'jobboard'); ?>">
            <?php jb_template_employer_application_status($application->app_status); ?>
        </td>
        <td class="column-actions column-3" data-colname="<?php esc_html_e('Actions', 'jobboard'); ?>">
            <?php jb_template_employer_application_actions($application->app_id); ?>
        </td>
    </tr>

<?php endforeach; ?>
