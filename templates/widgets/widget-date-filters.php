<?php
/**
 * The Template for displaying widgets date filters.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/widgets/widget-date-filters.php.
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

if (empty($times)){
    return;
}
?>

<div class="widget-content">
    <ul>

        <?php foreach ($times as $k => $v): ?>

        <li>
            <input id="date-filter-<?php echo esc_attr($k); ?>" type="radio" name="date-filters" value="<?php echo esc_attr($k); ?>" <?php checked($k, $value);?>/>
            <label for="date-filter-<?php echo esc_attr($k); ?>"><?php echo esc_html($v); ?></label>
        </li>

        <?php endforeach; ?>

    </ul>
</div>
