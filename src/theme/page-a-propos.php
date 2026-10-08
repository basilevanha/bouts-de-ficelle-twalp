<?php
/**
 * Template Name: A propos
 */

namespace App;

use Timber\Timber;

$context = Timber::context();
$post_id = $context['post']->ID;

$equipe = get_field( 'a-propos-equipe', $post_id ) ?: [];

$context['membres'] = array_map(
	fn ( $url ) => Timber::get_post( url_to_postid( $url ) ),
	$equipe['membres-liste'] ?? []
);

$context['hero']     = get_field( 'a-propos-hero', $post_id );
$context['groupe']   = get_field( 'a-propos-groupe', $post_id );
$context['equipe']   = $equipe;
$context['fichiers'] = get_field( 'a-propos-fichiers', $post_id );

Timber::render( 'templates/page-a-propos.twig', $context );
