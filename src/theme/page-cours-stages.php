<?php
/**
 * Template Name: Cours & stages
 */

namespace App;

use Timber\Timber;

$context = Timber::context();
$post_id = $context['post']->ID;

$context['hero']      = get_field( 'cours-hero', $post_id );
$context['offres']    = get_field( 'cours-offres', $post_id );
$context['gouts']     = get_field( 'cours-gouts', $post_id );
$context['stages']    = get_field( 'cours-stages', $post_id );
$context['spectacle'] = get_field( 'cours-spectacle', $post_id );

Timber::render( 'templates/page-cours-stages.twig', $context );
