<?php
/**
 * Plugins that hijack the theme call get_header() / get_footer().
 * Start an output buffer here; footer.php renders it into templates/page-plugin.twig.
 */

namespace App;

use Timber\Timber;

$GLOBALS['timberContext'] = Timber::context();

ob_start();
