<?php
/**
 * The template for displaying job search form
 *
 * This template can be overridden by copying it to yourtheme/jobboard/search-form.php.
 *
 * HOWEVER, on occasion JobBoard will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author  FOX
 * @package JobBoard/Templates
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<form role="search" method="get" class="jobboard-form search-form">
	<label class="screen-reader-text" for="jobboard-search-field"><?php echo esc_html($search['placeholder']); ?></label>
	<input type="search" id="jobboard-search-field" class="search-field" placeholder="<?php echo esc_attr($search['placeholder']); ?>" value="<?php echo esc_attr($search['value']); ?>" name="<?php echo esc_attr($search['name']); ?>" title="<?php echo esc_attr($search['placeholder']); ?>" />
	<input type="submit" value="<?php echo esc_attr($search['button']); ?>" />
    <input type="hidden" name="<?php echo esc_attr($search['type']) ?>" value="<?php echo esc_attr($search['type_value']) ?>" />
</form>
