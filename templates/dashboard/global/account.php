<?php
/**
 * The Template for displaying account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/global/account.php.
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

<div class="content-account">
    <div class="account-heading heading">
        <h3 class="title left"><?php esc_attr_e('Account Information', 'jobboard'); ?></h3>
        <a class="edit right" href="<?php echo esc_url(jb_page_endpoint_url('profile')); ?>"><?php esc_attr_e('Edit', 'jobboard'); ?></a>
    </div>
    <div class="account-content">
        <table>
            <tbody>

            <?php foreach ($fields as $k => $field): ?>

                <?php

                if(empty($field['value']) || $field['type'] != 'text'){
                    continue;
                }

                ?>

                <tr>
                    <th><?php echo esc_html($field['title']); ?></th>
                    <td><?php echo esc_html($field['value']); ?></td>
                </tr>

            <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>