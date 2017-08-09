<?php

namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Axolotl\UsesCustomTable;

/**
 * Class Run
 *
 * @package    Intraxia\Gistpen
 * @subpackage Model
 *
 * @property int        $ID
 * @property Collection $items
 * @property string     $job
 * @property string     $status
 * @property string     $scheduled_at
 * @property string     $started_at
 * @property string     $finished_at
 * @property string     $rest_url
 * @property string     $job_url
 */
class Run extends Model implements UsesCustomTable {

	/**
	 * {@inheritdoc}
	 *
	 * @var string[]
	 */
	public $fillable = array(
		'items',
		'job',
		'status',
		'scheduled_at',
		'started_at',
		'finished_at',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var string[]
	 */
	public $visible = array(
		'ID',
		'job',
		'status',
		'scheduled_at',
		'started_at',
		'finished_at',
		'rest_url',
		'job_url',
		'console_url',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	public static function get_table_name() {
		return 'runs';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	public static function get_primary_key() {
		return 'ID';
	}

	/**
	 * Computes the rest url property.
	 *
	 * @return string
	 */
	protected function compute_rest_url() {
		return rest_url( sprintf(
			'intraxia/v1/gistpen/jobs/%s/runs/%s',
			$this->job,
			$this->ID
		) );
	}

	/**
	 * Computes the job url property.
	 *
	 * @return string
	 */
	protected function compute_job_url() {
		return rest_url( sprintf(
			'intraxia/v1/gistpen/jobs/%s',
			$this->job
		) );
	}

	/**
	 * Computes the console url property.
	 *
	 * @return string
	 */
	protected function compute_console_url() {
		return rest_url( sprintf(
			'intraxia/v1/gistpen/jobs/%s/runs/%s/console',
			$this->job,
			$this->ID
		) );
	}
}
