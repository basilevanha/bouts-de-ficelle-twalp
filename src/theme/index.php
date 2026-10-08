<?php
/**
 * The main template file
 */

namespace App;

use Timber\Timber;

$templates = [ 'templates/index.twig' ];

if ( is_home() ) {
	array_unshift( $templates, 'templates/front-page.twig', 'templates/home.twig' );
}

$context          = Timber::context();
$context['posts'] = Timber::get_posts();

Timber::render( $templates, $context );
