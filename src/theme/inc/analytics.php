<?php
/**
 * Google Tag Manager
 *
 * Not loaded while the Vite dev server runs, so local visits
 * don't pollute the production analytics.
 */

namespace App;

const GTM_ID = 'GTM-M52HSCGM';

add_action( 'wp_head', function () {
	if ( vite_is_dev() ) {
		return;
	}
	?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','<?php echo esc_js( GTM_ID ); ?>');</script>
	<!-- End Google Tag Manager -->
	<?php
}, 0 );

add_action( 'wp_body_open', function () {
	if ( vite_is_dev() ) {
		return;
	}
	?>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( GTM_ID ); ?>"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?php
} );
