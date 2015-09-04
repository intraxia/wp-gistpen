<?php
namespace Intraxia\Gistpen\Model\Commit;

use Intraxia\Gistpen\Contract\GistAdapter;
use Intraxia\Gistpen\Model\Zip;

/**
 * Data object for an individual Commit
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Meta extends Zip implements GistAdapter {

	/**
	 * Head ID
	 *
	 * @var int
	 * @since    0.5.0
	 */
	protected $head_id = null;

	/**
	 * Gist ID for Head Zip
	 *
	 * @var   string
	 * @since 0.5.0
	 */
	protected $head_gist_id = 'none';

	/**
	 * Array of File States
	 *
	 * @var State[]
	 * @since 0.5.0
	 */
	protected $states = array();

	/**
	 * Get the Head Zip ID for the Commit
	 *
	 * @return int Head Zip ID
	 * @since 0.5.0
	 */
	public function get_head_id() {
		return $this->head_id;
	}

	/**
	 * Validate & set the Head Zip ID for the Commit
	 *
	 * @param int    $head_id     Head Zip ID ID
	 * @since 0.5.0
	 */
	public function set_head_id( $head_id ) {
		$this->head_id = (int) $head_id;
	}

	/**
	 * Get the Head Zip's Gist ID for the Commit
	 *
	 * @return string     Head Zip's Gist ID
	 * @since 0.5.0
	 */
    public function getGistSha()
    {
		return $this->head_gist_id;
	}

	/**
	 * Validate & set the Head Zip's Gist ID for the Commit
	 *
	 * @param int $head_gist_id Head Zip's Gist ID ID
	 * @since 0.5.0
	 */
    public function setGistSha($sha)
    {
        $this->head_gist_id = $sha;
	}

	/**
	 * Get the Array of States
	 *
	 * @return \Intraxia\Gistpen\Model\Commit\State[]
	 * @since  0.5.0
	 */
	public function get_states() {
		return $this->states;
	}

	/**
	 * Validate and add a State to the Commit
	 *
	 * @param State $state State model object
	 * @throws \Exception If not a State model object
	 * @since 0.5.0
	 */
	public function add_state( $state ) {
		if ( ! $state instanceof State ) {
			throw new \Exception( 'State objects only added to states array' );
		}

		$state_id = $state->get_ID();

		if ( null !== $state_id ) {
			$this->states[ $state_id ] = $state;
		} else {
			$this->states[] = $state;
		}
	}

    /**
     * @inheritdoc
     */
    public function toGist()
    {
        return $this->getGistSha() === 'none' ? $this->toCreateGist() : $this->toUpdateGist();
    }

    /**
     * Transforms the Commit Meta object into
     * a Gist-formatted array for Gist creation.
     *
     * @return array
     */
    protected function toCreateGist()
    {
        $gist = array(
            'description' => $this->get_description(),
        );

        $this->setGistStatus($gist);

        $states = $this->get_states();
        $files = array();

        foreach ($states as $state) {
            $files[$state->get_filename()] = array('content' => $state->get_code());
        }

        $gist['files'] = $files;

        return $gist;
    }

    /**
     * Transforms the Commit Meta object into
     * a Gist-formatted array for Gist updating.
     *
     * @return array
     */
    protected function toUpdateGist()
    {
        $gist = array(
            'description' => $this->get_description(),
        );

        $this->setGistStatus($gist);

        $states = $this->get_states();
        $files = array();

        foreach ($states as $state) {
            switch ($state->get_status()) {
                case 'new':
                    $files[$state->get_filename()] = array(
                        'content' => $state->get_code(),
                    );
                    break;
                case 'updated':
                    $files[$state->get_gist_id()] = array(
                        'content'  => $state->get_code(),
                        'filename' => $state->get_filename(),
                    );
                    break;
                case 'deleted':
                    $files[$state->get_gist_id()] = null;
                    break;
            }
        }

        $gist['files'] = $files;

        return $gist;
    }

    /**
     * Sets the status on the Gist array based on
     * the commit's status.
     *
     * @param  array  $gist   Array of Gist API data
     * @since  0.5.0
     */
    protected function setGistStatus(&$gist) {
        $gist['public'] = 'publish' === $this->get_status() ? true : false;
    }
}
