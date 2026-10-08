<?php
/**
 * ACF configuration
 *
 * Sets up ACF JSON save/load paths.
 */

namespace App;

/**
 * Set the path where ACF saves field group JSON files.
 */
add_filter('acf/settings/save_json', function () {
	return get_template_directory() . '/acf-json';
});

/**
 * Set the path(s) where ACF loads field group JSON files from.
 */
add_filter('acf/settings/load_json', function ($paths) {
	// Remove the default path
	unset($paths[0]);

	$paths[] = get_template_directory() . '/acf-json';

	return $paths;
});

/**
 * Create the private site-settings page on first boot if it doesn't exist yet.
 */
add_action('init', function () {
	if ( get_page_by_path('site-settings') ) {
		return;
	}

	wp_insert_post([
		'post_title'    => __('Réglages du site', 'bouts-de-ficelle'),
		'post_name'     => 'site-settings',
		'post_type'     => 'page',
		'post_status'   => 'private',
		'page_template' => 'page-site-settings.php',
	]);
});
