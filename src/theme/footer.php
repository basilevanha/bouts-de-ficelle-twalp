<?php
/**
 * Ends the output buffer started in header.php and renders the
 * plugin output into templates/page-plugin.twig.
 */

namespace App;

use Timber\Timber;

$timber_context = $GLOBALS['timberContext'] ?? null;

if ( ! isset( $timber_context ) ) {
	throw new \Exception( 'Timber context not set in footer.' );
}

$timber_context['content'] = ob_get_contents();
ob_end_clean();

Timber::render( 'templates/page-plugin.twig', $timber_context );
