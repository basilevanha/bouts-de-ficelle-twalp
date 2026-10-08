<?php
/**
 * Template Name: Ateliers
 */

namespace App;

use Timber\Timber;

$context = Timber::context();
$post_id = $context['post']->ID;

$liste = get_field( 'ateliers-liste', $post_id ) ?: [];

$context['hero']     = get_field( 'ateliers-hero', $post_id );
$context['liste']    = $liste;
$context['ateliers'] = array_map(
	fn ( $url ) => Timber::get_post( url_to_postid( $url ) ),
	$liste['items'] ?? []
);

Timber::render( 'templates/page-ateliers.twig', $context );
