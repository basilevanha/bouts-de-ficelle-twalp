<?php
/**
 * WP Event Manager integration
 *
 * Event dates and pictures are edited through ACF; this keeps the
 * WP Event Manager meta it relies on (times, expiry, banner) in sync.
 */

namespace App;

add_action( 'acf/save_post', function ( $post_id ) {
	if ( get_post_type( $post_id ) !== 'event_listing' ) {
		return;
	}

	update_post_meta( $post_id, '_event_start_time', get_post_meta( $post_id, '_event_start_date', true ) );
	update_post_meta( $post_id, '_event_end_time', get_post_meta( $post_id, '_event_end_date', true ) );
	update_post_meta( $post_id, '_event_expiry_date', get_post_meta( $post_id, '_event_end_date', true ) );
	update_post_meta( $post_id, '_event_banner', get_the_post_thumbnail_url( $post_id ) );
} );

/**
 * List the events (editions) linked to an atelier or a spectacle.
 *
 * An event points to its atelier/spectacle through the ACF "type_name"
 * link field, and declares its kind in "event-type".
 *
 * @param int    $post_id ID of the atelier or spectacle.
 * @param string $type    'atelier' or 'spectacle'.
 * @return array[] title, date, end, url of each matching event.
 */
function get_linked_events( int $post_id, string $type ): array {
	$events = [];

	$event_ids = get_posts(
		[
			'post_type'      => 'event_listing',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		]
	);

	foreach ( $event_ids as $event_id ) {
		if ( get_field( 'event-type', $event_id ) !== $type ) {
			continue;
		}

		if ( url_to_postid( home_url( (string) get_field( 'type_name', $event_id ) ) ) !== $post_id ) {
			continue;
		}

		$events[] = [
			'title' => get_the_title( $event_id ),
			'date'  => get_post_meta( $event_id, '_event_start_date', true ),
			'end'   => get_post_meta( $event_id, '_event_end_date', true ),
			'url'   => get_permalink( $event_id ),
		];
	}

	return $events;
}
