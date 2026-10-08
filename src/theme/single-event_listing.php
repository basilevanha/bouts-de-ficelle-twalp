<?php
/**
 * Single event (WP Event Manager)
 */

namespace App;

use Timber\Timber;

$context = Timber::context();
$post_id = $context['post']->ID;

$context['infos'] = [
	'start'    => get_event_datetime( $post_id, '_event_start_date' ),
	'end'      => get_event_datetime( $post_id, '_event_end_date' ),
	'location' => get_post_meta( $post_id, '_event_location', true ),
];

$context['content']              = get_field( 'acf-content', $post_id );
$context['typeEvent']            = get_field( 'event-type', $post_id );
$context['reservation']          = get_field( 'event-reservation', $post_id );
$context['isBillable']           = get_field( 'event-paid', $post_id );
$context['price']                = get_field( 'event-price', $post_id );
$context['registrationForm']     = get_field( 'event-shortcode', $post_id );
$context['registrationFormFree'] = get_field( 'event-shortcode-free', $post_id );

// The atelier/spectacle this event belongs to.
$edition_id = url_to_postid( home_url( (string) get_field( 'type_name', $post_id ) ) );

$context['typeName']      = get_the_title( $edition_id );
$context['typeURL']       = get_permalink( $edition_id );
$context['ateliersURL']   = get_permalink( get_page_id_by_template( 'page-ateliers.php' ) );
$context['spectaclesURL'] = get_permalink( get_page_id_by_template( 'page-spectacles.php' ) );

Timber::render( 'templates/single-event_listing.twig', $context );
