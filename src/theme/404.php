<?php
/**
 * The template for displaying 404 pages (Not Found)
 */

namespace App;

use Timber\Timber;

$context = Timber::context();

Timber::render( 'templates/404.twig', $context );
