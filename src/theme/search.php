<?php
/**
 * Search results page
 */

namespace App;

use Timber\Timber;

$context          = Timber::context();
$context['posts'] = Timber::get_posts();

Timber::render( [ 'templates/search.twig', 'templates/archive.twig', 'templates/index.twig' ], $context );
