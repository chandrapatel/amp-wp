<?php
/**
 * Test AMP post template functions.
 *
 * @package AMP
 */

use AmpProject\AmpWP\Tests\Helpers\AssertContainsCompatibility;

/**
 * Class Test_AMP_Post_Template_Functions
 */
class Test_AMP_Post_Template_Functions extends WP_UnitTestCase {

	use AssertContainsCompatibility;

	public function setUp() {
		parent::setUp();
		require_once AMP__DIR__ . '/includes/amp-post-template-functions.php';
	}

	/** @covers ::amp_post_template_init_hooks() */
	public function test_amp_post_template_init_hooks() {
		amp_post_template_init_hooks();
		$this->assertSame( 10, has_action( 'amp_post_template_head', 'noindex' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_head', 'amp_post_template_add_title' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_head', 'amp_post_template_add_canonical' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_head', 'amp_post_template_add_fonts' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_head', 'amp_add_generator_metadata' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_head', 'wp_generator' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_head', 'amp_post_template_add_block_styles' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_head', 'amp_post_template_add_default_styles' ) );
		$this->assertSame( 99, has_action( 'amp_post_template_css', 'amp_post_template_add_styles' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_footer', 'amp_post_template_add_analytics_data' ) );
		$this->assertSame( 10, has_action( 'admin_bar_init', [ 'AMP_Theme_Support', 'init_admin_bar' ] ) );
		$this->assertSame( 10, has_action( 'amp_post_template_footer', 'wp_admin_bar_render' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_head', 'wp_print_head_scripts' ) );
		$this->assertSame( 10, has_action( 'amp_post_template_footer', 'wp_print_footer_scripts' ) );
	}

	/** @covers ::amp_post_template_add_title() */
	public function test_amp_post_template_add_title() {
		$post = self::factory()->post->create();
		$this->go_to( get_permalink( $post ) );
		$template = new AMP_Post_Template( $post );

		$output = get_echo(
			function () use ( $template ) {
				amp_post_template_add_title( $template );
			}
		);

		$this->assertStringContains( wp_get_document_title(), $output );
		$this->assertStringContains( '<title>', $output );
	}

	/** @covers ::amp_post_template_add_canonical() */
	public function test_amp_post_template_add_canonical() {
		$post = self::factory()->post->create();
		$this->go_to( get_permalink( $post ) );
		$template = new AMP_Post_Template( $post );
		$output   = get_echo(
			function () use ( $template ) {
				amp_post_template_add_canonical( $template );
			}
		);
		$this->assertStringContains( '<link rel="canonical"', $output );
		$this->assertStringContains( wp_get_canonical_url(), $output );
	}

	/** @covers ::amp_post_template_add_fonts() */
	public function test_amp_post_template_add_fonts() {
		$post     = self::factory()->post->create();
		$template = new AMP_Post_Template( $post );
		$output   = get_echo(
			function () use ( $template ) {
				amp_post_template_add_fonts( $template );
			}
		);
		$this->assertEmpty( $output );

		add_filter(
			'amp_post_template_data',
			function ( $data ) {
				$data['font_urls'] = [
					'https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap',
				];
				return $data;
			}
		);
		$template = new AMP_Post_Template( $post );
		$output   = get_echo(
			function () use ( $template ) {
				amp_post_template_add_fonts( $template );
			}
		);
		$this->assertStringContains( 'Roboto', $output );
	}

	/** @covers ::amp_post_template_add_block_styles() */
	public function test_amp_post_template_add_block_styles() {
		$output = get_echo( 'amp_post_template_add_block_styles' );
		$this->assertTrue( current_theme_supports( 'wp-block-styles' ) );
		if ( function_exists( 'wp_common_block_scripts_and_styles' ) ) {
			$this->assertContains( 'wp-block-library-css', $output );
			$this->assertSame( 1, did_action( 'enqueue_block_assets' ) );
		}
	}

	/** @covers ::amp_post_template_add_default_styles() */
	public function test_amp_post_template_add_default_styles() {
		$output = get_echo( 'amp_post_template_add_default_styles' );
		$this->assertStringContains( 'amp-default', $output );
	}

	/** @covers ::amp_post_template_add_styles() */
	public function test_amp_post_template_add_styles() {
		$post = self::factory()->post->create();
		add_filter(
			'amp_post_template_data',
			function ( $data ) {
				$data['post_amp_stylesheets'] = [
					'body{color:red}',
				];
				$data['post_amp_styles']      = [
					'body' => [
						'color:blue',
					],
				];
				return $data;
			}
		);
		$template = new AMP_Post_Template( $post );
		$output   = get_echo(
			function () use ( $template ) {
				amp_post_template_add_styles( $template );
			}
		);

		$this->assertStringContains( 'body{color:red', $output );
		$this->assertStringContains( 'body{color:blue', $output );
	}

	/** @covers ::amp_post_template_add_analytics_data() */
	public function test_amp_post_template_add_analytics_data() {
		$this->assertEmpty( get_echo( 'amp_post_template_add_analytics_data' ) );

		add_filter(
			'amp_analytics_entries',
			static function () {
				return [
					'foo' => [
						'attributes'  => [],
						'config_data' => [
							'bar' => 'baz',
						],
					],
				];
			}
		);

		$output = get_echo( 'amp_post_template_add_analytics_data' );
		$this->assertStringContains( '<amp-analytics', $output );
	}
}
