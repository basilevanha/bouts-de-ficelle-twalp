<?php
/**
 * The template for displaying all pages.
 */

namespace App;

use Timber\Timber;

$context = Timber::context();

Timber::render(
	[ 'templates/page-' . $context['post']->post_name . '.twig', 'templates/page.twig' ],
	$context
);
