<?php
/**
 * Template Name: Spectacles
 */

namespace App;

use Timber\Timber;

$context = Timber::context();
$post_id = $context['post']->ID;

$liste = get_field( 'spectacles-liste', $post_id ) ?: [];

$spectacles = array_map(
	fn ( $url ) => Timber::get_post( url_to_postid( $url ) ),
	$liste['items'] ?? []
);

// One filter button per show type (each type only once).
$filters = [];
foreach ( $spectacles as $spectacle ) {
	if ( ! in_array( $spectacle->showtype, $filters, false ) ) {
		$filters[] = $spectacle->showtype;
	}
}

$context['hero']       = get_field( 'spectacles-hero', $post_id );
$context['liste']      = $liste;
$context['spectacles'] = $spectacles;
$context['filters']    = $filters;

Timber::render( 'templates/page-spectacles.twig', $context );
