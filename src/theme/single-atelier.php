<?php
/**
 * Single atelier
 */

namespace App;

use Timber\Timber;

$context = Timber::context();

$context['evenements'] = get_linked_events( $context['post']->ID, 'atelier' );

Timber::render( 'templates/single-atelier.twig', $context );
