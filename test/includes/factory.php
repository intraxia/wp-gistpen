<?php

class WP_Gistpen_UnitTest_Factory extends WP_UnitTest_Factory {
	/**
	 * @var WP_UnitTest_Factory_For_Gistpen
	 */
	public $gistpen;

	function __construct() {
		parent::__construct();
		$this->gistpen = new WP_UnitTest_Factory_For_Gistpen( $this );
	}
}

class WP_UnitTest_Factory_For_Gistpen extends WP_UnitTest_Factory_For_Post {

	function __construct( $factory = null ) {
		parent::__construct( $factory );
		$this->default_generation_definitions = array(
			'post_status' => 'publish',
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Post title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'Post content %s' ),
			'post_excerpt' => new WP_UnitTest_Generator_Sequence( 'Post excerpt %s' ),
			'post_type' => 'gistpen'
		);
	}

}
