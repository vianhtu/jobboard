<?php
/**
 * The Template for displaying input checkbox.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/fields/fields/checkbox.php.
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

<ul id="<?php echo esc_attr($id); ?>" class="field-<?php echo esc_attr($type); ?>">

    <?php foreach ($options as $key => $val): ?>

    <li>
        <input id="<?php echo sprintf('%s-%s', $id, $key); ?>" type="checkbox" name="<?php echo esc_attr($name); ?>" class="checkbox" value="<?php echo esc_attr($key); ?>"<?php jb_checked($value, $key); ?>>
        <label for="<?php echo sprintf('%s-%s', $id, $key); ?>"><?php echo esc_html($val); ?></label>
    </li>

    <?php endforeach; ?>

</ul>
