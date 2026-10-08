<?php
/**
 * SVG icon helper
 *
 * Renders an icon from the generated sprite (theme/assets/icons/sprite.svg,
 * built by bin/build-sprite.js from src/icons/*.svg).
 *
 * Exposed to Twig as the icon() function — see StarterSite::add_functions_to_twig().
 */

namespace App;

/**
 * Build the <svg><use> markup for one sprite icon.
 *
 * Sizing and color are driven entirely by CSS classes (Tailwind) — pass
 * e.g. 'h-5 w-5 text-primary'. There are deliberately no width/height
 * arguments: HTML size attributes would just be overridden by any class
 * and would not follow responsive variants.
 *
 * @param string $name  Icon name — the src/icons/ file name without .svg.
 * @param string $class Extra CSS classes (e.g. Tailwind "h-5 w-5 text-primary").
 * @return string Safe HTML — registered as is_safe, so no |raw needed in Twig.
 */
function render_icon( string $name, string $class = '' ): string {
	$sprite_url = get_template_directory_uri() . '/assets/icons/sprite.svg';

	$classes = trim( 'icon ' . $class );
	$href    = esc_url( $sprite_url ) . '#icon-' . esc_attr( $name );

	return sprintf(
		'<svg class="%s" aria-hidden="true" focusable="false"><use href="%s"></use></svg>',
		esc_attr( $classes ),
		$href
	);
}
