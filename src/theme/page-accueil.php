<?php
/**
 * Template Name: Accueil
 */

namespace App;

use Timber\Timber;

$context = Timber::context();
$post_id = $context['post']->ID;

$ateliers = get_field( 'accueil-ateliers', $post_id ) ?: [];

$items = [];
foreach ( $ateliers['items'] ?? [] as $url ) {
	$atelier_id = url_to_postid( $url );
	$items[]    = [
		'icon' => get_field( 'icon', $atelier_id ),
		'post' => Timber::get_post( $atelier_id ),
	];
}

$context['hero']     = get_field( 'accueil-hero', $post_id );
$context['actus']    = get_field( 'accueil-actus', $post_id );
$context['cours']    = get_field( 'accueil-cours', $post_id );
$context['ateliers'] = [
	'infos' => $ateliers,
	'items' => $items,
];

Timber::render( 'templates/page-accueil.twig', $context );
