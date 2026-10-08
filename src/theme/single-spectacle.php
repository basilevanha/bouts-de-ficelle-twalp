<?php
/**
 * Single spectacle
 */

namespace App;

use Timber\Timber;

$context = Timber::context();

$context['evenements'] = get_linked_events( $context['post']->ID, 'spectacle' );

Timber::render( 'templates/single-spectacle.twig', $context );
