<?php
namespace Intraxia\Gistpen\Test;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use League\FactoryMuffin\Exceptions\SaveFailedException;
use League\FactoryMuffin\Stores\StoreInterface;

class MuffinStore implements StoreInterface {
	/**
	 * Models managed by Store.
	 *
	 * @var \Intraxia\Jaxion\Axolotl\Model[]
	 */
	public $saved = [];

	/**
	 * Models not yet saved by Store.
	 *
	 * @var \Intraxia\Jaxion\Axolotl\Model[]
	 */
	public $pending = [];

	/**
	 * Store for use with FactoryMuffin.
	 *
	 * @param EntityManager $em Jaxion EntityManager impelementation.
	 */
	public function __construct( EntityManager $em ) {
	  $this->em = $em;
	}

	/**
	 * Save the model to the database.
	 *
	 * @param object $model The model instance.
	 *
	 * @throws SaveFailedException
	 *
	 * @return void
	 */
	public function persist($model) {
		$ret = $this->em->persist($model);

		if ( is_wp_error( $ret ) ) {
			throw new SaveFailedException( get_class( $model ) );
		}

		$this->saved[] = $ret;
	}

	/**
	 * Return an array of models waiting to be saved.
	 *
	 * @return \Intraxia\Jaxion\Axolotl\Model[]
	 */
	public function pending() {
		return $this->pending;
	}

	/**
	 * Mark a model as waiting to be saved.
	 *
	 * @param object $model The model instance.
	 *
	 * @return void
	 */
	public function markPending($model) {
		$this->pending[] = $model;
	}

	/**
	 * Is the model waiting to be saved?
	 *
	 * @param object $model The model instance.
	 *
	 * @return bool
	 */
	public function isPending($model) {
		return in_array( $model, $this->pending );
	}

	/**
	 * Return an array of saved models.
	 *
	 * @return \Intraxia\Jaxion\Axolotl\Model[]
	 */
	public function saved() {
		return $this->saved;
	}

	/**
	 * Mark a model as saved.
	 *
	 * @param object $model The model instance.
	 *
	 * @return void
	 */
	public function markSaved($model) {
		$this->saved[] = $model;
	}

	/**
	 * Is the model saved?
	 *
	 * @param object $model The model instance.
	 *
	 * @return bool
	 */
	public function isSaved($model) {
		return in_array( $model, $this->saved );
	}

	/**
	 * Delete all the saved models.
	 *
	 * @throws \League\FactoryMuffin\Exceptions\DeletingFailedException
	 *
	 * @return void
	 */
	public function deleteSaved() {
		foreach ( $this->saved as $value ) {
			$this->em->delete( $value );
		}
	}
}
