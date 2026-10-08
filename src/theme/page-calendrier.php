<?php
/**
 * Template Name: Calendrier
 */

namespace App;

use Timber\Timber;

$context = Timber::context();

Timber::render( 'templates/page-calendrier.twig', $context );
