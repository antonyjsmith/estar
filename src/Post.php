<?php
namespace EStar;

class Post {
	private $sanitizer;

	public function __construct( $sanitizer ) {
		$this->sanitizer = $sanitizer;

		add_action( 'customize_register', [ $this, 'register' ] );
		add_filter( 'body_class', [ $this, 'add_body_classes' ] );
		add_filter( 'post_class', [ $this, 'add_post_classes' ] );

		add_filter( 'previous_post_link', [ $this, 'previous_post_link' ], 10, 4 );
		add_filter( 'next_post_link', [ $this, 'next_post_link' ], 10, 4 );
	}

	public function register( $wp_customize ) {
		$wp_customize->add_section( 'post', [
			'title'    => esc_html__( 'Post', 'estar' ),
			'priority' => Customizer::get_priority( 'post' ),
		] );

		$wp_customize->add_setting( 'post_layout', [
			'sanitize_callback' => [ $this->sanitizer, 'sanitize_choice' ],
			'default'           => 'sidebar-right',
		] );
		$wp_customize->add_control( 'post_layout', [
			'label'   => esc_html__( 'Layout', 'estar' ),
			'section' => 'post',
			'type'    => 'select',
			'choices' => [
				'sidebar-right' => __( 'Sidebar Right', 'estar' ),
				'sidebar-left'  => __( 'Sidebar Left', 'estar' ),
				'no-sidebar'    => __( 'No Sidebar', 'estar' ),
			],
		] );

		$wp_customize->add_setting( 'post_thumbnail', [
			'sanitize_callback' => [ $this->sanitizer, 'sanitize_choice' ],
			'default'           => 'thumbnail-before-header',
		] );
		$wp_customize->add_control( 'post_thumbnail', [
			'label'   => esc_html__( 'Thumbnail Position', 'estar' ),
			'section' => 'post',
			'type'    => 'select',
			'choices' => [
				'thumbnail-header-background' => __( 'As Post Header Background', 'estar' ),
				'thumbnail-before-header'     => __( 'Before Post Header', 'estar' ),
				'thumbnail-after-header'      => __( 'After Post Header', 'estar' ),
				'no-thumbnail'                => __( 'Do Not Display', 'estar' ),
			],
		] );

		$wp_customize->add_setting( 'post_header_align', [
			'sanitize_callback' => [ $this->sanitizer, 'sanitize_choice' ],
			'default'           => 'left',
		] );
		$wp_customize->add_control( 'post_header_align', [
			'label'   => esc_html__( 'Post Header Alignment', 'estar' ),
			'section' => 'post',
			'type'    => 'select',
			'choices' => [
				'left'   => __( 'Left', 'estar' ),
				'right'  => __( 'Right', 'estar' ),
				'center' => __( 'Center', 'estar' ),
			],
		] );
	}

	public function add_body_classes( $classes ) {
		if ( ! is_singular() ) {
			return $classes;
		}
		$classes[] = 'singular';

		$thumbnail_position = self::get_thumbnail_position();
		if ( has_post_thumbnail() || 'thumbnail-header-background' !== $thumbnail_position ) {
			$classes[] = $thumbnail_position;
		}

		if ( ! is_single() ) {
			return $classes;
		}
		$classes[] = 'entry-header-' . get_theme_mod( 'post_header_align', 'left' );
		return $classes;
	}

	public function add_post_classes( $classes ) {
		$classes[] = 'entry';
		return $classes;
	}

	public function previous_post_link( $output, $format, $link, $adjacent_post ) {
		if ( empty( $adjacent_post ) ) {
			return $output;
		}
		global $post;
		$post = $adjacent_post;
		setup_postdata( $post );

		ob_start();
		get_template_part( 'template-parts/content/adjacent', 'previous' );
		wp_reset_postdata();

		return ob_get_clean();
	}

	public function next_post_link( $output, $format, $link, $adjacent_post ) {
		if ( empty( $adjacent_post ) ) {
			return $output;
		}
		global $post;
		$post = $adjacent_post;
		setup_postdata( $post );

		ob_start();
		get_template_part( 'template-parts/content/adjacent', 'next' );
		wp_reset_postdata();

		return ob_get_clean();
	}

	public static function date() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		echo $time_string; // WPCS: OK.
	}

	public static function author() {
		$byline = sprintf(
			'<span class="author vcard">%s <a class="url fn n" href="%s">%s</a></span>',
			get_avatar( get_the_author_meta( 'user_email' ), 24 ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
		echo $byline; // WPCS: OK.
	}

	public static function categories() {
		the_category();
	}

	public static function tags() {
		$tags = get_the_tag_list( '', '' );
		if ( $tags ) {
			echo '<div class="tags">', $tags, '</div>'; // WPCS: OK.
		}
	}

	public static function get_thumbnail_position() {
		$type = is_page() ? 'page' : 'post';
		return apply_filters( 'estar_post_thumbnail_position', get_theme_mod( "{$type}_thumbnail", 'thumbnail-before-header' ) );
	}

	public static function get_thumbnail_size() {
		$thumbnail_position = self::get_thumbnail_position();
		$layout             = Layout::get_layout();

		return 'thumbnail-header-background' === $thumbnail_position || 'no-sidebar' === $layout ? 'full' : 'medium_large';
	}

	public static function get_thumbnail_class() {
		$thumbnail_position = self::get_thumbnail_position();
		$layout             = Layout::get_layout();

		return 'thumbnail-header-background' === $thumbnail_position ? 'alignfull' : ( 'no-sidebar' === $layout ? 'alignwide' : '' );
	}
}