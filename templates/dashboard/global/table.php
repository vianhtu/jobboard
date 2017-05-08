<?php
/**
 * The Template for displaying table.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/global/table.php.
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

if (empty($table) || empty($columns)){
    return;
}
?>

<div class="<?php echo esc_attr($table); ?>-table jobboard-table">
    <div class="table-wrap">
        <table id="table-<?php echo esc_attr($table); ?>" class="table">
            <tbody>
            <tr>

            <?php $row = 0; foreach ($columns as $key => $column): ?>

                <th id="column-<?php echo esc_attr($key); ?>" class="column-<?php echo esc_attr($row); ?>"><?php echo esc_html__($column); ?></th>

            <?php $row++; endforeach; ?>

            </tr>

            <?php if($jobs && $jobs->have_posts()): ?>

            <?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>

            <tr>

            <?php $row = 0; foreach ($columns as $key => $column): ?>

                <td class="<?php echo esc_attr($key); ?> column-<?php echo esc_attr($key); ?> column-<?php echo esc_attr($row); ?>" data-colname="<?php echo esc_attr($column); ?>">
                    <?php echo apply_filters("jobboard_table_{$table}_{$key}", ''); ?>
                    <?php if($row == 0): ?>
                    <button type="button" class="toggle-row">
                        <span class="screen-reader-text"><?php esc_html_e('More details', 'jobboard'); ?></span>
                    </button>
                    <?php endif; $row++; ?>
                </td>

            <?php endforeach; ?>

            </tr>

            <?php endwhile; ?>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>
