<?php
/**
 * Tests for the plugin's admin integration.
 *
 * @package Schemify
 */

namespace Schemify\Admin;

use WP_Mock as M;
use Schemify;

class AdminTest extends Schemify\TestCase {

	protected $testFiles = array(
		'admin.php',
		'core.php',
	);

	public function testAddMetaBoxes() {
		M::wpFunction( 'add_meta_box', array(
			'times' => '1',
			'args'  => array( 'schemify', '*', __NAMESPACE__ . '\meta_box_cb', '*' ),
		) );

		M::wpPassthruFunction( '_x' );

		add_meta_boxes();
	}

	public function testMetaBoxCb() {
		$post = new \stdClass;
		$post->ID = 123;
		$post->post_type = 'post';

		$post_type = new \stdClass;
		$post_type->labels = new \stdClass;
		$post_type->labels->singular_name = 'Post';

		M::wpFunction( 'get_post_type_object', array(
			'times'  => 1,
			'return' => $post_type,
		) );

		M::wpFunction( 'wp_json_encode', array(
			'times'  => 1,
			'return' => '{"json":"data"}',
		) );

		M::wpFunction( 'Schemify\Core\build_object', array(
			'times'  => 1,
		) );

		M::wpPassthruFunction( '__' );
		M::wpPassthruFunction( 'esc_html' );

		ob_start();
		meta_box_cb( $post );
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertContains( '{"json":"data"}', $result );
	}
}
