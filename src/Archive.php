<?php
namespace EStar;

class Archive {
	private $sanitizer;

	public function __construct( $sanitizer ) {
		$this->sanitizer = $sanitizer;

		add_action( 'customize_register', [ $this, 'register' ] );

		add_filter( 'excerpt_more', [ $this, 'continue_reading_link' ] );
		add_filter( 'the_content_more_link', [ $this, 'continue_reading_link' ] );

		add_filter( 'excerpt_length', [ $this, 'change_excerpt_length' ] );

		add_filter( 'get_the_archive_title', [ $this, 'change_archive_title' ] );
	}

	public function register( $wp_customize ) {
		$wp_customize->add_section( 'archive', [
			'title'    => esc_html__( 'Archive', 'estar' ),
			'priority' => '1200',
		] );
		$wp_customize->add_setting( 'archive_content', [
			'sanitize_callback' => [ $this->sanitizer, 'sanitize_choice' ],
			'default'           => 'excerpt',
		] );
		$wp_customize->add_control( 'archive_content', [
			'label'   => esc_html__( 'Content Display', 'estar' ),
			'section' => 'archive',
			'type'    => 'select',
			'choices' => [
				'content' => __( 'Content', 'estar' ),
				'excerpt' => __( 'Excerpt', 'estar' ),
			],
		] );

		$wp_customize->add_setting( 'archive_excerpt_length', [
			'sanitize_callback' => 'absint',
			'default'           => 55,
		] );
		$wp_customize->add_control( 'archive_excerpt_length', [
			'label'   => esc_html__( 'Excerpt Length', 'estar' ),
			'section' => 'archive',
			'type'    => 'number',
		] );

		$wp_customize->add_setting( 'archive_continue_text', [
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => __( 'Continue reading', 'estar' ),
		] );
		$wp_customize->add_control( 'archive_continue_text', [
			'label'   => esc_html__( 'Continue Reading Text', 'estar' ),
			'section' => 'archive',
			'type'    => 'text',
		] );
	}

	public function continue_reading_link() {
		$text = Settings::get( 'archive_continue_text' );
		$text .= the_title( ' <span class="screen-reader-text">', '</span>', false );
		return '<p class="more"><a class="more-link" href="' . esc_url( get_permalink() ) . '">' . wp_kses_post( $text ) . '</a></p>';
	}

	public function change_excerpt_length( $length ) {
		return Settings::get( 'archive_excerpt_length' );
	}

	public function change_archive_title( $title ) {
		if ( is_category() || is_tag() || is_tax() ) {
			$title = single_term_title( '', false );
		}
		return $title;
	}
}