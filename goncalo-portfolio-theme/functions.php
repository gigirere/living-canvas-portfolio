<?php
/**
 * Gonçalo Gonçalves — Portfolio 2026 (block theme).
 *
 * Content is authored with Gutenberg blocks. The "Card" block (goncalo/card)
 * renders each draggable card; the page H1 and the "thinking" notes are plain
 * core blocks placed in the page, positioned by CSS. A small vanilla-JS layer
 * (assets/js/portfolio.js) turns the cards into a draggable canvas on every
 * screen size, with show/hide, bring-to-front, resize and reset controls.
 *
 * @package goncalo-portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'GP_VERSION', '2.0.0' );
define( 'GP_DIR', get_template_directory() );
define( 'GP_URI', get_template_directory_uri() );

/**
 * Theme supports. Block themes get most of this from theme.json, but these
 * still need declaring in PHP.
 */
function gp_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	load_theme_textdomain( 'goncalo-portfolio', GP_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'gp_setup' );

/**
 * Front-end assets.
 */
function gp_enqueue_assets() {
	// Full core block stylesheet so any core block is styled correctly inside
	// cards and on normal pages, regardless of per-block asset loading.
	wp_enqueue_style( 'wp-block-library' );

	wp_enqueue_style(
		'gp-portfolio',
		GP_URI . '/assets/css/portfolio.css',
		array( 'wp-block-library' ), // our overrides load after core block styles
		GP_VERSION
	);

	wp_enqueue_script(
		'gp-portfolio',
		GP_URI . '/assets/js/portfolio.js',
		array(),
		GP_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'gp_enqueue_assets' );

/**
 * Register the "Card" block. The block name and attributes are unchanged from
 * v1 so previously saved cards keep rendering.
 */
function gp_register_blocks() {
	wp_register_script(
		'gp-card-editor',
		GP_URI . '/blocks/card/index.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		GP_VERSION,
		true
	);

	register_block_type( GP_DIR . '/blocks/card' );
}
add_action( 'init', 'gp_register_blocks' );

/**
 * Register a block category so the Card block is easy to find.
 *
 * @param array $categories Existing categories.
 * @return array
 */
function gp_block_categories( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'goncalo',
				'title' => __( 'Portfolio', 'goncalo-portfolio' ),
				'icon'  => null,
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'gp_block_categories' );

/**
 * Register the starter pattern (the full homepage: H1, thinking notes, cards).
 */
function gp_register_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'goncalo',
		array( 'label' => __( 'Portfolio', 'goncalo-portfolio' ) )
	);

	$file = GP_DIR . '/patterns/starter-cards.html';
	if ( ! is_readable( $file ) ) {
		return;
	}

	$pattern = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( false !== $pattern ) {
		register_block_pattern(
			'goncalo/starter-cards',
			array(
				'title'      => __( 'Portfolio — homepage (H1, notes, cards)', 'goncalo-portfolio' ),
				'categories' => array( 'goncalo' ),
				'content'    => $pattern,
			)
		);
	}
}
add_action( 'init', 'gp_register_patterns' );

/**
 * Render the floating control chip: "GG" wordmark (links home, resets the
 * canvas), one swatch per card (filled by JS) and the show/hide cards toggle.
 *
 * Rendered in PHP rather than as a block so the controls always exist and can
 * never be deleted from the Site Editor by accident.
 */
function gp_render_controls() {
	$eye_off = '<svg width="24" height="24" viewBox="0 0 192 192" aria-hidden="true" focusable="false"><path d="M40.44 25.965A6 6 0 1 0 31.56 34.035L45.99 49.912C18.75 66.63 7.035 92.4 6.518 93.57a6 6 0 0 0 0 4.875c0.262 0.593 6.615 14.678 20.737 28.8C46.072 146.055 69.84 156 96 156a95.332 95.332 90 0 0 39.053-8.123l16.5 18.158a6 6 0 1 0 8.88-8.07Zm35.498 56.88l31.252 34.387a24 24 0 0 1-31.252-34.387ZM96 144c-23.085 0-43.252-8.393-59.948-24.938A99.87 99.87 0 0 1 18.75 96c3.518-6.592 14.745-25.043 35.513-37.035l13.5 14.812a36 36 0 0 0 47.745 52.5l11.047 12.15A84 84 0 0 1 96 144Zm4.5-71.573a6 6 0 0 1 2.25-11.79 36.12 36.12 0 0 1 29.078 31.98 6 6 0 0 1-5.415 6.533 4.792 4.792 90 0 1-0.563 0 6 6 0 0 1-6-5.445A24.068 24.068 0 0 0 100.5 72.427Zm84.96 26.018c-0.315 0.705-7.913 17.527-25.02 32.85a6 6 0 1 1-8.002-8.94A99.578 99.578 0 0 0 173.288 96a99.863 99.863 0 0 0-17.34-23.078C139.252 56.392 119.085 48 96 48a88.778 88.778 0 0 0-14.52 1.177A6 6 0 1 1 79.5 37.343 100.5 100.5 0 0 1 96 36c26.16 0 49.927 9.945 68.745 28.763 14.122 14.122 20.475 28.215 20.737 28.807A6 6 0 0 1 185.483 98.445Z" fill="currentColor"/></svg>';

	$eye_on = '<svg width="24" height="24" viewBox="0 0 192 192" aria-hidden="true" focusable="false"><path d="M185.483 93.57c-0.262-0.592-6.615-14.685-20.738-28.807C145.928 45.945 122.16 36 96 36S46.072 45.945 27.255 64.763C13.133 78.885 6.78 92.977 6.518 93.57a6 6 0 0 0 0 4.875c0.262 0.593 6.615 14.678 20.737 28.8C46.072 146.055 69.84 156 96 156s49.928-9.945 68.745-28.755c14.123-14.122 20.476-28.207 20.738-28.8a6 6 0 0 0 0-4.875ZM96 144c-23.085 0-43.252-8.393-59.948-24.938A99.87 99.87 0 0 1 18.75 96a99.87 99.87 0 0 1 17.302-23.062C52.748 56.392 72.915 48 96 48s43.252 8.392 59.948 24.938A99.87 99.87 0 0 1 173.288 96C169.14 103.748 151.05 144 96 144Zm0-84a36 36 0 1 0 36 36 36.045 36.045 0 0 0-36-36Zm0 60a24 24 0 1 1 24-24 24.027 24.027 0 0 1-24 24Z" fill="currentColor"/></svg>';
	?>
	<div class="pf-controls" data-pf-controls>
		<a class="pf-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" data-pf-reset aria-label="<?php esc_attr_e( 'Home (reset cards)', 'goncalo-portfolio' ); ?>">GG</a>

		<div class="pf-swatches" data-pf-swatches></div>

		<button type="button" class="pf-toggle" data-pf-toggle aria-pressed="false" aria-label="<?php esc_attr_e( 'Hide cards', 'goncalo-portfolio' ); ?>" title="<?php esc_attr_e( 'Hide cards', 'goncalo-portfolio' ); ?>">
			<span class="pf-toggle__hide" aria-hidden="true"><?php echo $eye_off; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span class="pf-toggle__show" aria-hidden="true"><?php echo $eye_on; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</button>
	</div>
	<?php
}

/**
 * Skip link + the control chip, in that DOM order, so the first focusable
 * element on the page is "skip to content" (WCAG 2.4.1).
 */
function gp_render_body_open() {
	printf(
		'<a class="pf-skip-link" href="#content">%s</a>',
		esc_html__( 'Skip to content', 'goncalo-portfolio' )
	);
	gp_render_controls();
}
add_action( 'wp_body_open', 'gp_render_body_open' );

/**
 * Core injects its own skip link into block templates; ours is emitted first
 * (above), so drop core's to avoid two skip links.
 */
function gp_remove_core_skip_link() {
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_block_template_skip_link' );
	remove_action( 'wp_footer', 'the_block_template_skip_link' );
}
add_action( 'init', 'gp_remove_core_skip_link' );

/* =========================================================================
   Audit fixes — <head> metadata, structured data, headers, robots, llms.txt
   ========================================================================= */

/**
 * Build a meta description for the current view.
 *
 * @return string
 */
function gp_meta_description() {
	$desc = '';
	$post = is_singular() ? get_queried_object() : null;

	// An explicit excerpt always wins — it is the editable, controllable source.
	if ( $post instanceof WP_Post && $post->post_excerpt ) {
		$desc = wp_strip_all_tags( $post->post_excerpt, true );
	} elseif ( is_front_page() ) {
		// The homepage is a card canvas; scraping it reads as noise, so prefer
		// the site tagline (Settings → General) and let an excerpt override it.
		$desc = get_bloginfo( 'description', 'display' );
	} elseif ( $post instanceof WP_Post ) {
		$desc = wp_strip_all_tags( strip_shortcodes( $post->post_content ), true );
	}

	if ( '' === trim( (string) $desc ) ) {
		$desc = get_bloginfo( 'description', 'display' );
	}

	$desc = trim( preg_replace( '/\s+/', ' ', (string) $desc ) );

	return wp_html_excerpt( $desc, 160, '…' );
}

/**
 * Best available sharing image: featured image, then the Site Icon.
 *
 * @return string URL or ''.
 */
function gp_share_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$url = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );
		if ( $url ) {
			return $url;
		}
	}
	$icon = get_site_icon_url( 512 );
	return $icon ? $icon : '';
}

/**
 * Output meta description, Open Graph / Twitter cards, theme-color and
 * color-scheme. Skipped when an SEO plugin already handles Open Graph.
 */
function gp_head_meta() {
	if ( is_404() ) {
		return;
	}

	$title = wp_get_document_title();
	$desc  = gp_meta_description();
	$image = gp_share_image();
	$url   = is_singular() ? get_permalink() : home_url( '/' );

	// The design is a light, warm paper canvas — declare it so the browser
	// chrome matches and dark mode does not force a white flash.
	echo '<meta name="theme-color" content="#DED8CC" />' . "\n";
	echo '<meta name="color-scheme" content="light" />' . "\n";

	if ( $desc ) {
		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
	}

	// Defer Open Graph to Yoast / Rank Math / SEOPress when one is active.
	if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}

	$og_type = ( is_singular() && ! is_front_page() ) ? 'article' : 'website';
	printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $og_type ) );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( get_locale() ) );

	if ( $desc ) {
		printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
	}

	if ( $image ) {
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	} else {
		echo '<meta name="twitter:card" content="summary" />' . "\n";
	}

	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $desc ) );
	}
}
add_action( 'wp_head', 'gp_head_meta', 5 );

/**
 * JSON-LD: a Person for the portfolio owner plus the WebSite itself, so search
 * engines and AI agents can identify whose work this is.
 */
function gp_json_ld() {
	if ( ! is_front_page() && ! is_singular() ) {
		return;
	}

	$person = array(
		'@type' => 'Person',
		'name'  => 'Gonçalo Gonçalves',
		'url'   => home_url( '/' ),
		'jobTitle' => 'UX/UI & Graphic Designer',
	);

	$graph = array(
		array(
			'@type'     => 'WebSite',
			'@id'       => home_url( '/#website' ),
			'url'       => home_url( '/' ),
			'name'      => get_bloginfo( 'name' ),
			'inLanguage' => get_bloginfo( 'language' ),
			'publisher' => $person,
		),
		$person,
	);

	if ( is_singular() && ! is_front_page() ) {
		$graph[] = array(
			'@type'         => 'CreativeWork',
			'@id'           => get_permalink() . '#work',
			'url'           => get_permalink(),
			'name'          => get_the_title(),
			'description'   => gp_meta_description(),
			'author'        => $person,
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
		);
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	echo '<script type="application/ld+json">'
		. wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. '</script>' . "\n";
}
add_action( 'wp_head', 'gp_json_ld', 6 );

/**
 * Conservative security headers the theme can safely set. HSTS and a full CSP
 * belong at the server/host level — see README.
 */
function gp_security_headers( $headers ) {
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	// Clickjacking: allow same-origin framing only (WP admin previews need it).
	if ( ! isset( $headers['X-Frame-Options'] ) ) {
		$headers['X-Frame-Options'] = 'SAMEORIGIN';
	}
	return $headers;
}
add_filter( 'wp_headers', 'gp_security_headers' );

/**
 * robots.txt: point crawlers at the sitemap and state AI-crawler policy
 * explicitly (allowed by default — flip to Disallow to opt out).
 */
function gp_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}

	// Core already prints the XML sitemap line; only add the AI-facing index.
	$extra  = "\n# AI/agent index\n";
	$extra .= '# llms.txt: ' . esc_url_raw( home_url( '/llms.txt' ) ) . "\n";
	$extra .= "\n# AI crawlers — allowed. Change Allow to Disallow to opt out.\n";

	foreach ( array( 'GPTBot', 'ClaudeBot', 'anthropic-ai', 'PerplexityBot', 'Google-Extended', 'CCBot', 'Applebot-Extended' ) as $bot ) {
		$extra .= "User-agent: {$bot}\nAllow: /\n\n";
	}

	return $output . $extra;
}
add_filter( 'robots_txt', 'gp_robots_txt', 10, 2 );

/**
 * Serve /llms.txt — a plain-text index of the site for AI agents.
 */
function gp_llms_txt() {
	$path = strtok( $_SERVER['REQUEST_URI'] ?? '', '?' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( '/llms.txt' !== untrailingslashit( (string) $path ) ) {
		return;
	}

	nocache_headers();
	header( 'Content-Type: text/plain; charset=utf-8' );
	status_header( 200 );

	// Plain text: decode any HTML entities coming from site options/content.
	$plain = static function ( $text ) {
		$text = wp_strip_all_tags( (string) $text, true );
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		return trim( preg_replace( '/\s+/', ' ', $text ) );
	};

	$lines   = array();
	$lines[] = '# ' . $plain( get_bloginfo( 'name' ) );
	$tagline = $plain( get_bloginfo( 'description' ) );
	if ( $tagline ) {
		$lines[] = '';
		$lines[] = '> ' . $tagline;
	}
	$lines[] = '';
	$lines[] = 'Portfolio of Gonçalo Gonçalves — UX/UI and graphic design.';
	$lines[] = 'Site: ' . home_url( '/' );
	$lines[] = '';
	$lines[] = '## Pages';
	$lines[] = '';

	$posts = get_posts(
		array(
			'post_type'      => array( 'page', 'post' ),
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);

	$front_id = (int) get_option( 'page_on_front' );

	foreach ( $posts as $p ) {
		if ( $p->post_excerpt ) {
			$summary = $plain( $p->post_excerpt );
		} elseif ( (int) $p->ID === $front_id ) {
			// The homepage is a card canvas; its raw text reads as noise.
			$summary = $tagline;
		} else {
			$summary = wp_html_excerpt( $plain( strip_shortcodes( $p->post_content ) ), 120, '…' );
		}

		$lines[] = sprintf(
			'- [%s](%s)%s',
			$plain( $p->post_title ),
			get_permalink( $p ),
			$summary ? ': ' . $summary : ''
		);
	}

	$lines[] = '';
	echo implode( "\n", $lines ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'template_redirect', 'gp_llms_txt' );

/**
 * A small, unobtrusive privacy-policy link (bottom-left, matching the H1's
 * 10px muted style) — only rendered when a privacy page is actually set.
 */
function gp_privacy_link() {
	$id = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( ! $id || 'publish' !== get_post_status( $id ) ) {
		return;
	}
	printf(
		'<a class="pf-privacy" href="%s">%s</a>',
		esc_url( get_permalink( $id ) ),
		esc_html( get_the_title( $id ) )
	);
}
add_action( 'wp_footer', 'gp_privacy_link' );
