<?php
/**
 * The Template for displaying all single posts
 */

namespace App;

use Timber\Timber;

$context = Timber::context();
$post    = $context['post'];

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'templates/single-password.twig', $context );
} else {
	Timber::render(
		[
			'templates/single-' . $post->ID . '.twig',
			'templates/single-' . $post->post_type . '.twig',
			'templates/single-' . $post->slug . '.twig',
			'templates/single.twig',
		],
		$context
	);
}
