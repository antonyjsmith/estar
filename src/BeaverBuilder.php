<?php
namespace EStar;

use FLThemeBuilderLayoutData;

class BeaverBuilder {
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'add_theme_support' ] );
		add_filter( 'fl_theme_builder_part_hooks', [ $this, 'register_part_hooks' ] );

		add_action( 'wp', [ $this, 'render_header' ] );
		add_action( 'wp', [ $this, 'render_footer' ] );
	}

	public function add_theme_support() {
		add_theme_support( 'fl-theme-builder-headers' );
		add_theme_support( 'fl-theme-builder-footers' );
		add_theme_support( 'fl-theme-builder-parts' );
	}

	public function register_part_hooks() {
		return [
			[
				'label' => __( 'Site', 'estar' ),
				'hooks' => [
					'estar_body_top'    => __( 'Body Top', 'estar' ),
					'estar_body_bottom' => __( 'Body Bottom', 'estar' ),
				],
			],
			[
				'label' => __( 'Header', 'estar' ),
				'hooks' => [
					'estar_header_before' => __( 'Before Header', 'estar' ),
					'estar_header_after'  => __( 'After Header', 'estar' ),
				],
			],
			[
				'label' => __( 'Content', 'estar' ),
				'hooks' => [
					'estar_content_before' => __( 'Before Content', 'estar' ),
					'estar_content_after'  => __( 'After Content', 'estar' ),
				],
			],
			[
				'label' => __( 'Footer', 'estar' ),
				'hooks' => [
					'estar_footer_before' => __( 'Before Footer', 'estar' ),
					'estar_footer_after'  => __( 'After Footer', 'estar' ),
				],
			],
			[
				'label' => __( 'Sidebar', 'estar' ),
				'hooks' => [
					'estar_sidebar_top' => __( 'Sidebar Top', 'estar' ),
					'estar_sidebar_bottom'  => __( 'Sidebar Bottom', 'estar' ),
				],
			],
			[
				'label' => __( 'Loop', 'estar' ),
				'hooks' => [
					'loop_start' => __( 'Before Loop', 'estar' ),
					'loop_end'   => __( 'After Loop', 'estar' ),
				],
			],
			[
				'label' => __( 'Entry', 'estar' ),
				'hooks' => [
					'estar_entry_before'         => __( 'Before Entry', 'estar' ),
					'estar_entry_after'          => __( 'After Entry', 'estar' ),
					'estar_entry_header_before'  => __( 'Before Entry Header', 'estar' ),
					'estar_entry_header_after'   => __( 'After Entry Header', 'estar' ),
					'estar_entry_content_before' => __( 'Before Entry Content', 'estar' ),
					'estar_entry_content_after'  => __( 'After Entry Content', 'estar' ),
					'estar_entry_footer_before'  => __( 'Before Entry Footer', 'estar' ),
					'estar_entry_footer_after'   => __( 'After Entry Footer', 'estar' ),
					'estar_comments_before'      => __( 'Before Comments', 'estar' ),
					'estar_comments_after'       => __( 'After Comments', 'estar' ),
				],
			],
		];
	}

	public function render_header() {
		$header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids();
		if ( empty( $header_ids ) ) {
			return;
		}
		add_filter( 'estar_header_enabled', '__return_false' );
		add_action( 'estar_header', 'FLThemeBuilderLayoutRenderer::render_header' );
	}

	public function render_footer() {
		$footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids();
		if ( empty( $footer_ids ) ) {
			return;
		}
		add_filter( 'estar_footer_enabled', '__return_false' );
		add_action( 'estar_footer', 'FLThemeBuilderLayoutRenderer::render_footer' );
	}
}