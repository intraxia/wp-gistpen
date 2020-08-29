<?php

namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Model\Repo;
use Intraxia\Jaxion\Axolotl\GuardedPropertyException;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class RepoController
 *
 * @package    Intraxia\Gistpen
 * @subpackage Http
 * @since      1.0.0
 */
class RepoController {
	/**
	 * Database interface service.
	 *
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * RepoController constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * Retrieves a collection of Repos based on the provided params.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function index( WP_REST_Request $request ) {
		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );

		$collection = $this->em->find_by(
			Repo::class,
			array(
				'offset' => ( $page - 1 ) * $per_page,
				'limit'  => $per_page,
				'with'   => array(
					'blobs' => array(
						'with' => 'language',
					),
				),
			)
		);

		if ( is_wp_error( $collection ) ) {
			$collection->add_data( array( 'status' => 500 ) );

			return $collection;
		}

		$response = new WP_REST_Response( $collection->serialize(), 200 );

		$response->header( 'X-WP-Total', (int) $collection->query->found_posts );
		$response->header( 'X-WP-TotalPages', (int) $collection->query->max_num_pages );

		return $response;
	}

	/**
	 * Creates a new Repo based on the provided params and returns it.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create( WP_REST_Request $request ) {
		$model = $this->em->create( Repo::class, [
			'description' => $request->get_param( 'description' ),
			'status'      => $request->get_param( 'status' ),
			'password'    => $request->get_param( 'password' ),
			'sync'        => $request->get_param( 'sync' ),
			'blobs'       => array_map(function( $blob ) {
				return [
					'filename' => $blob['filename'],
					// @TODO(mAAdhaTTah) this is duplicated with the filter but isn't set correctly when run thru REST API.
					// shouldn't core set this property by default?
					'code'     => isset( $blob['code'] ) ? $blob['code'] : '',
					'language' => [
						// @TODO(mAAdhaTTah) this is a bad API for the EntityManager.
						'slug' => isset( $blob['language'] ) ? $blob['language'] : 'none',
					],
				];
			}, $request->get_param( 'blobs' ) ),
		] );

		if ( is_wp_error( $model ) ) {
			$model->add_data( array( 'status' => 500 ) );

			return $model;
		}

		$response = new WP_REST_Response( $model->serialize(), 201 );
		$response->header( 'Location', $model->rest_url );

		return $response;
	}

	/**
	 * Retrieves a Repo for the provided id.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function view( WP_REST_Request $request ) {
		$model = $this->em->find( Repo::class, $request->get_param( 'id' ), array(
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		if ( is_wp_error( $model ) ) {
			$model->add_data( array( 'status' => 404 ) );

			return $model;
		}

		return new WP_REST_Response( $model->serialize(), 200 );
	}

	/**
	 * Updates all of the model's properties with the request params.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$model = $this->em->find( Repo::class, $id, array(
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		if ( is_wp_error( $model ) ) {
			$model->add_data( array( 'status' => 404 ) );

			return $model;
		}

		$model->refresh( $request->get_json_params() );
		$model = $this->em->persist( $model );

		if ( is_wp_error( $model ) ) {
			$model->add_data( array( 'status' => 500 ) );

			return $model;
		}

		$model = $this->em->find( Repo::class, $id, array(
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		if ( is_wp_error( $model ) ) {
			$model->add_data( array( 'status' => 500 ) );

			return $model;
		}

		return new WP_REST_Response( $model->serialize(), 200 );
	}

	/**
	 * Applies a patch to the model's properties of the request params.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function apply( WP_REST_Request $request ) {
		$id    = $request->get_param( 'id' );
		$model = $this->em->find( Repo::class, $id, array(
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		if ( is_wp_error( $model ) ) {
			$model->add_data( array( 'status' => 404 ) );

			return $model;
		}

		$model->merge( $request->get_json_params() );
		$model = $this->em->persist( $model );

		if ( is_wp_error( $model ) ) {
			$model->add_data( array( 'status' => 500 ) );

			return $model;
		}

		$model = $this->em->find( Repo::class, $id, array(
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		if ( is_wp_error( $model ) ) {
			$model->add_data( array( 'status' => 500 ) );

			return $model;
		}

		return new WP_REST_Response( $model->serialize(), 200 );
	}

	/**
	 * Sends the model to the trash or deletes it from the database.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function trash( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$model = $this->em->find( Repo::class, $id, array(
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		if ( is_wp_error( $model ) ) {
			$model->add_data( array( 'status' => 404 ) );

			return $model;
		}

		$result = $this->em->delete( $model, false );

		if ( is_wp_error( $result ) ) {
			$result->add_data( array( 'status' => 500 ) );

			return $result;
		}

		return new WP_REST_Response( null, 204 );
	}
}
