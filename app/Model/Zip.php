<?php
namespace Intraxia\Gistpen\Model;

use WP_Post;

/**
 * Manages the Gistpen's zip data
 *
 * Acts as a container for all the files that an
 * individual Gistpen can hold, as well as metadata
 * about the Gistpen.
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Zip {

	/**
	 * Zip description
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $description = '';

	/**
	 * Files contained by the Zip
	 *
	 * @var File[]
	 * @since 0.4.0
	 */
	protected $files = array();

	/**
	 * Post's ID
	 *
	 * @var int
	 * @since 0.4.0
	 */
	protected $ID = null;

	/**
	 * Post's status
	 *
	 * @var string
	 * @since 0.5.0
	 */
	protected $status = '';

	/**
	 * Post's password
	 *
	 * @var string
	 * @since 0.5.0
	 */
	protected $password = '';

	/**
	 * Zips's Gist ID
	 *
	 * @var string
	 * @since 0.5.0
	 */
	protected $gist_id = 'none';

	/**
	 * Zips's sync status
	 *
	 * @var   string
	 * @since 0.5.0
	 */
	protected $sync = 'off';

	/**
	 * Date created in GMT
	 *
	 * @var string
	 * @since    0.5.0
	 */
	protected $create_date = '';

    /**
     * Data from constructor.
     *
     * @var array|WP_Post
     */
    protected $source;

    /**
     * Constructs a new Zip model for a given source.
     *
     * The Zip Model constructor accepts an array or a WP_Post object and uses these
     * data sources to set the default data on the new model.
     *
     * @param array|WP_Post $source
     */
    public function __construct($source = array())
    {
        if (is_array($source)) {
            $this->buildByArray($source);
        }

        if ($source instanceof WP_Post) {
            $this->buildByPost($source);
        }

        if ($source) {
            $this->source = $source;
        }
    }

    protected function buildByArray($array)
    {
        $array = array_intersect_key($array, array_flip(array('description', 'ID', 'status', 'password', 'gist_id', 'sync', 'create_date')));

        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }
    }

    protected function buildByPost($post)
    {
        if (isset($post->ID)) {
            $this->set_ID( $post->ID );
        }
        if ( isset( $post->post_title ) ) {
            $this->set_description( $post->post_title );
        }
        if ( isset( $post->post_status ) ) {
            $this->set_status( $post->post_status );
        }
        if ( isset( $post->post_password ) ) {
            $this->set_password( $post->post_password );
        }
        if ( isset( $post->gist_id ) ) {
            $this->set_gist_id( $post->gist_id );
        }
        if ( isset( $post->sync ) ) {
            $this->set_sync( $post->sync );
        }
        if ( isset( $post->post_date_gmt ) ) {
            $this->set_create_date( $post->post_date_gmt );
        }
    }

	/**
	 * Get the zip's description
	 *
	 * @return string
	 * @since 0.5.0
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Validate and set the zip's description
	 *
	 * @param string $description Zip's description
	 * @since 0.5.0
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * Get the zip's files
	 *
	 * @return File[]
	 * @since 0.5.0
	 */
	public function get_files() {
		return $this->files;
	}

	/**
	 * Validate and add a file to the zip
	 *
	 * @param File $file File model object
	 * @since 0.5.0
	 */
	public function add_file(File $file)
    {
		$file_id = $file->get_ID();

        if ($file_id) {
			$this->files[ $file_id ] = $file;
		} else {
            // @todo if we add a file to the array, and the new file has no ID
            // but the array already has some elements, it'll be added with a key numerically
            // +1 from the last ID, which isn't necessarily where we want it, and could
            // hypothetically, cause conflicts if another file gets added with that ID
            // this is unlikely, given how WordPress saves revision in the same posts
            // table as everything else (meaning the next ID for a given post is likely its revision)
            // but may still be something worth looking out for. i've forgetten why we index
            // by ID, but i bet there's a reason.
			$this->files[] = $file;
		}
	}

	/**
	 * Add an array of files to the zip
	 *
	 * @param array $files Array of Files model objects
	 * @since 0.5.0
	 */
	public function add_files( $files ) {
		foreach ( $files as $file ) {
			$this->add_file( $file );
		}
	}

	/**
	 * Get the zip's DB ID
	 *
	 * @return int File's db ID
	 * @since  0.4.0
	 */
	public function get_ID() {
		return $this->ID;
	}

	/**
	 * Set the zip's DB ID as integer
	 *
	 * @param  int $ID DB id
	 * @since  0.5.0
	 */
	public function set_ID( $ID ) {
		$this->ID = (int) $ID;
	}

	/**
	 * Get the zip's post status
	 * @return string Zip's post_status
	 * @since 0.5.0
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Set the zip's post status
	 *
	 * @param string $status Zip's post_status
	 * @since 0.5.0
	 */
	public function set_status( $status ) {
		// @todo this needs validation
		$this->status = $status;
	}

	/**
	 * Get the zip's password
	 *
	 * @return string Zip's post_password
	 * @since 0.5.0
	 */
	public function get_password() {
		return $this->password;
	}

	/**
	 * Set the zip's password
	 *
	 * @param string $password Zip's post_password
	 * @since 0.5.0
	 */
	public function set_password( $password ) {
		// @todo what kind of data does this need to be? hashed, etc.?
		$this->password = $password;
	}

	/**
	 * Get the zip's Gist ID
	 *
	 * @return string Zip's Gist ID
	 * @since  0.5.0
	 */
	public function get_gist_id() {
		return $this->gist_id;
	}

	/**
	 * Set the zip's Gist ID
	 *
	 * @param string $gist_id Zip's Gist ID
	 * @since  0.5.0
	 */
	public function set_gist_id( $gist_id ) {
		$this->gist_id = $gist_id;
	}

	/**
	 * Get the zip's sync status
	 *
	 * @return bool Zip's sync status
	 * @since  0.5.0
	 */
	public function get_sync() {
		return $this->sync;
	}

	/**
	 * Set the zip's sync status
	 *
	 * @param  string   $sync Zip's sync status
	 * @since  0.5.0
	 */
	public function set_sync( $sync ) {
		if ( 'on' !== $sync ) {
			$sync = 'off';
		}

		$this->sync = $sync;
	}

	/**
	 * Get the date this Commit was made
	 *
	 * @return string Date created in GMT
	 * @since  0.5.0
	 */
	public function get_create_date() {
		return $this->create_date;
	}

	/**
	 * Validate & set the date this Commit was made
	 *
	 * @return string Date created in GMT
	 * @since  0.5.0
	 */
	public function set_create_date( $create_date ) {
		// @todo validate date
		$this->create_date = $create_date;
	}

	/**
	 * Get's the zip's post content for display
	 * on the front-end
	 *
	 * @return string Zip's post content
	 * @since 0.4.0
	 */
	public function get_post_content() {
		$post_content = '';

		if ( ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$post_content .= $file->get_post_content();
			}
		}

		return $post_content;
	}

	/**
	 * Get's the zip's shortcode content for display
	 * on the front-end
	 *
	 * @return string Zip's post content
	 * @since 0.4.0
	 */
	public function get_shortcode_content() {
		$shortcode_content = '';

		if ( ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$shortcode_content .= $file->get_shortcode_content();
			}
		}

		return $shortcode_content;
	}

}
