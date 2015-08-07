<?php
use Intraxia\Gistpen\View\Settings;
/**
 * @group register
 */
class View_Settings_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		global $title;

		parent::setUp();
		$app = Intraxia\Gistpen\App::get();
		$this->settings = new Settings($app['basename'], $app['path']);
		$title = "Settings page";
	}

	function test_check_settings_page_valid_html() {
		ob_start();
		$this->settings->display_plugin_admin_page();
		$html = ob_get_contents();
		ob_end_clean();

		$this->assertValidHTML( $html );
	}

	function test_check_github_user_layout_valid_html() {
		set_transient( '_wpgp_github_token_user_info', array(
			'login'               => 'username',
			'id'                  => 1234567890,
			'avatar_url'          => 'https://avatars.githubusercontent.com/u/1234567890?v=3',
			'gravatar_id'         => '',
			'url'                 => 'https://api.github.com/users/username',
			'html_url'            => 'https://github.com/username',
			'followers_url'       => 'https://api.github.com/users/username/followers',
			'following_url'       => 'https://api.github.com/users/username/following{/other_user}',
			'gists_url'           => 'https://api.github.com/users/username/gists{/gist_id}',
			'starred_url'         => 'https://api.github.com/users/username/starred{/owner}{/repo}',
			'subscriptions_url'   => 'https://api.github.com/users/username/subscriptions',
			'organizations_url'   => 'https://api.github.com/users/username/orgs',
			'repos_url'           => 'https://api.github.com/users/username/repos',
			'events_url'          => 'https://api.github.com/users/username/events{/privacy}',
			'received_events_url' => 'https://api.github.com/users/username/received_events',
			'type'                => 'User',
			'site_admin'          => false,
			'name'                => 'Jane Doe',
			'company'             => 'Super Awesome Company',
			'blog'                => 'http://www.example.com',
			'location'            => 'Big City',
			'email'               => 'email@example.com',
			'hireable'            => false,
			'bio'                 => NULL,
			'public_repos'        => 32,
			'public_gists'        => 3,
			'followers'           => 4,
			'following'           => 0,
			'created_at'          => '2013-05-08T02:48:05Z',
			'updated_at'          => '2014-12-28T17:18:50Z',
			'private_gists'       => 2,
			'total_private_repos' => 0,
			'owned_private_repos' => 0,
			'disk_usage'          => 4966,
			'collaborators'       => 0,
			'plan'                => array(
				'name'          => 'free',
				'space'         => 307200,
				'collaborators' => 0,
				'private_repos' => 0,
			),
		) );

		ob_start();
		$this->settings->github_user_layout();
		$html = ob_get_contents();
		ob_end_clean();

		$this->assertValidHTML( $html );
	}

	function tearDown() {
		parent::tearDown();
	}
}
