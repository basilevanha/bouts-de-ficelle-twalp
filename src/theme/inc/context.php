<?php
/**
 * Global Timber context
 *
 * Data shared by every template (header, footer, highlighted event…).
 * The "GLOBAL - Informations générales" ACF fields live on the homepage.
 */

namespace App;

use DateTime;
use Timber\Timber;

/**
 * Find the ID of the first page using a given page template.
 *
 * Replaces the hard-coded page IDs of the old theme, which differed
 * between environments (DEV = 9 / STAGING = 10…).
 *
 * @param string $template Template file name, e.g. 'page-accueil.php'.
 * @return int Page ID, or 0 if no page uses this template.
 */
function get_page_id_by_template( string $template ): int {
	static $cache = [];

	if ( ! isset( $cache[ $template ] ) ) {
		$pages = get_posts(
			[
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => $template,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'orderby'        => 'ID',
				'order'          => 'ASC',
			]
		);

		$cache[ $template ] = $pages ? (int) $pages[0] : 0;
	}

	return $cache[ $template ];
}

/**
 * Convert a WP Event Manager date meta (stored without timezone)
 * into a DateTime in the site timezone.
 */
function get_event_datetime( int $post_id, string $meta_key ): DateTime {
	return new DateTime( (string) get_post_meta( $post_id, $meta_key, true ), wp_timezone() );
}

add_filter( 'timber/context', function ( $context ) {
	$home_id        = get_page_id_by_template( 'page-accueil.php' );
	$ateliers_id    = get_page_id_by_template( 'page-ateliers.php' );
	$spectacles_id  = get_page_id_by_template( 'page-spectacles.php' );

	$highlight      = get_field( 'global-highlight', $home_id ) ?: [];
	$highlight_link = $highlight['event'] ?? '';
	$event_id       = $highlight_link ? url_to_postid( home_url( $highlight_link ) ) : 0;

	$context['global'] = [
		'themeLink'      => get_template_directory_uri(),
		'logo'           => get_field( 'global-logo', $home_id ),
		'highlight'      => [
			// No event selected (or expired) → hide the highlight block.
			'toggle'       => $highlight_link ? ( $highlight['toggle'] ?? false ) : false,
			'label'        => $highlight['label'] ?? '',
			'type'         => get_field( 'event-type', $event_id ),
			'categoryName' => get_the_title( url_to_postid( (string) get_field( 'type_name', $event_id ) ) ),
			'categoryUrl'  => get_field( 'type_name', $event_id ),
			'title'        => get_the_title( $event_id ),
			'url'          => get_permalink( $event_id ),
			'image'        => get_the_post_thumbnail_url( $event_id ),
			'start'        => get_event_datetime( $event_id, '_event_start_date' ),
			'location'     => get_post_meta( $event_id, '_event_location', true ),
		],
		'socials'        => get_field( 'global-socials', $home_id ),
		'footer'         => get_field( 'global-footer', $home_id ),
		'copyrights'     => get_field( 'global-copyrights', $home_id ),
		'pageAteliers'   => $ateliers_id ? Timber::get_post( $ateliers_id ) : null,
		'pageSpectacles' => $spectacles_id ? Timber::get_post( $spectacles_id ) : null,
	];

	$context['timezone'] = wp_timezone_string();

	return $context;
} );
