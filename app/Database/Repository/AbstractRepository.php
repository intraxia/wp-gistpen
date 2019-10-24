<?php

namespace Intraxia\Gistpen\Database\Repository;

use Intraxia\Gistpen\Contract\Repository;
use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\State;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\Model;
use InvalidArgumentException;
use stdClass;
use WP_Error;
use WP_Term;

abstract class AbstractRepository implements Repository {

	/**
	 * Meta prefix.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * AbstractRepository constructor.
	 *
	 * @param EntityManager $em
	 * @param string        $prefix
	 */
	public function __construct( EntityManager $em, $prefix ) {
		$this->prefix = $prefix;
		$this->em = $em;
	}

	protected function fill_relations( Model $model, array $params ) {
		if ( ! isset( $params['with'] ) ) {
			$params['with'] = array();
		}

		if ( is_string( $params['with'] ) ) {
			$params['with'] = array( $params['with'] => array() );
		}

		if ( ! is_array( $params['with'] ) ) {
			throw new InvalidArgumentException( 'with' );
		}

		foreach ( $params['with'] as $key => $params ) {
			$value = null;

			switch ( $key ) {
				case 'blobs';
					$value = $this->em->find_by( \Intraxia\Gistpen\Database\EntityManager::BLOB_CLASS, array_merge( $params, array(
						'repo_id'     => $model->get_primary_id(),
						'post_status' => 'any',
						'order'       => 'ASC',
						'orderby'     => 'date',
					) ) );
					break;
				case 'language':
					$terms = get_the_terms( $model->get_primary_id(), Language::get_taxonomy() );

					if ( $terms ) {
						$term = array_pop( $terms );
					} else {
						$term       = new WP_Term( new stdClass );
						$term->slug = 'none';
					}

					$value = new Language( array( Model::OBJECT_KEY => $term ) );
					break;
				case 'states':
					$value = new Collection( \Intraxia\Gistpen\Database\EntityManager::STATE_CLASS );

					foreach ( $model->state_ids as $state_id ) {
						/** @var State|WP_Error $state */
						$state = $this->find( \Intraxia\Gistpen\Database\EntityManager::STATE_CLASS, $state_id, $params );

						if ( ! is_wp_error( $state ) ) {
							$value = $value->add( $state );
						}
					}
					break;
			}

			if ( null !== $value ) {
				$model->set_attribute( $key, $value );
			}
		}

		// @todo remove when we have instances saved
		$model->sync_original();

		return $model;
	}

	/**
	 * Wraps the given key with the string required to make it a meta key.
	 *
	 * @param {string} $key Key to turn into meta key.
	 *
	 * @return string Generated meta key.
	 */
	protected function make_meta_key( $key ) {
		return "_{$this->prefix}_{$key}";
	}
}
