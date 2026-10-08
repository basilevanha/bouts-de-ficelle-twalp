<?php

/**
 * StarterSite class
 * This class is used to add custom functionality to the theme.
 */

namespace App;

use Timber\Site;
use Timber\Timber;

/**
 * Class StarterSite.
 */
class StarterSite extends Site {



	/**
	 * StarterSite constructor.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'theme_supports' ] );
		add_action( 'init', [ $this, 'register_post_types' ] );
		add_action( 'init', [ $this, 'register_taxonomies' ] );
		// Styles/scripts are handled by inc/vite.php

		add_filter( 'timber/context', [ $this, 'add_to_context' ] );
		add_filter( 'timber/twig/filters', [ $this, 'add_filters_to_twig' ] );
		add_filter( 'timber/twig/functions', [ $this, 'add_functions_to_twig' ] );
		add_filter( 'timber/twig/environment/options', [ $this, 'update_twig_environment_options' ] );

		parent::__construct();
	}

	/**
	 * This is where you can register custom post types.
	 */
	public function register_post_types() {}

	/**
	 * This is where you can register custom taxonomies.
	 */
	public function register_taxonomies() {}

	/**
	 * This is where you add some context.
	 *
	 * @param array $context context['this'] Being the Twig's {{ this }}
	 */
	public function add_to_context( $context ) {
		// Menus by location; falls back to the existing menus (by slug)
		// as long as no menu is assigned in Appearance → Menus.
		$context['menus'] = [
			'header' => Timber::get_menu( 'header' ),
			'footer' => Timber::get_menu( 'footer' ) ?? Timber::get_menu( 'navigation' ),
		];
		$context['site']  = $this;

		// @acf-start
		$settings_page = get_page_by_path( 'site-settings' );
		if ( $settings_page && function_exists( 'pll_get_post' ) ) {
			$translated_id = pll_get_post( $settings_page->ID );
			if ( $translated_id ) {
				$settings_page = get_post( $translated_id );
			}
		}
		$context['site_settings'] = ( $settings_page && function_exists( 'get_fields' ) )
			? get_fields( $settings_page->ID )
			: [];
		// @acf-end

		return $context;
	}

	/**
	 * This is where you can add your theme supports.
	 */
	public function theme_supports() {
		// Register navigation menus
		register_nav_menus(
			[
				'header' => _x( 'Header menu', 'Backend - menu name', 'bouts-de-ficelle' ),
				'footer' => _x( 'Footer menu', 'Backend - menu name', 'bouts-de-ficelle' ),
			]
		);

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			[
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			]
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			[
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			]
		);

		add_theme_support( 'menus' );
	}

	/**
	 * This is where you can add your own filters to twig.
	 *
	 * @link https://timber.github.io/docs/v2/hooks/filters/#timber/twig/filters
	 * @param array $filters an array of Twig filters.
	 */
	public function add_filters_to_twig( $filters ) {
		return $filters;
	}


	/**
	 * This is where you can add your own functions to twig.
	 *
	 * @link https://timber.github.io/docs/v2/hooks/filters/#timber/twig/functions
	 * @param array $functions an array of existing Twig functions.
	 */
	public function add_functions_to_twig( $functions ) {
		$additional_functions = [
			'get_theme_mod' => [
				'callable' => 'get_theme_mod',
			],
			'icon' => [
				'callable' => __NAMESPACE__ . '\\render_icon',
				// render_icon() escapes every dynamic value (esc_attr/esc_url)
				// and returns trusted markup, so Twig must not re-escape it —
				// otherwise {{ icon(...) }} would print the <svg> source.
				'options'  => [ 'is_safe' => [ 'html' ] ],
			],
		];

		return array_merge( $functions, $additional_functions );
	}

	/**
	 * Updates Twig environment options.
	 *
	 * @see https://twig.symfony.com/doc/2.x/api.html#environment-options
	 *
	 * @param array $options an array of environment options
	 *
	 * @return array
	 */
	public function update_twig_environment_options( $options ) {
		// Templates migrated from the old theme print ACF/WordPress HTML as-is
		// (post content, WYSIWYG fields, pre-encoded titles): keep escaping off.
		$options['autoescape'] = false;

		return $options;
	}
}
