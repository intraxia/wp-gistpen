<?php
namespace Intraxia\Gistpen\Test;

use WP_UnitTest_Factory;
use WP_UnitTest_Factory_For_Post;
use WP_UnitTest_Generator_Sequence;

class Factory extends WP_UnitTest_Factory {
	/**
	 * @var WP_UnitTest_Factory_For_Gistpen
	 */
	public $gistpen;

	public function __construct() {
		parent::__construct();
		$this->gistpen = new WP_UnitTest_Factory_For_Gistpen( $this );
	}
}

// @codingStandardsIgnoreLine
class WP_UnitTest_Factory_For_Gistpen extends WP_UnitTest_Factory_For_Post {

	public function __construct( $factory = null ) {
		parent::__construct( $factory );
		$this->default_generation_definitions = array(
			'post_status'  => 'publish',
			'post_title'   => new WP_UnitTest_Generator_Sequence( 'Post title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'Post content %s' ),
			'post_excerpt' => new WP_UnitTest_Generator_Sequence( 'Post excerpt %s' ),
			'post_type'    => 'gistpen',
		);
	}
}
