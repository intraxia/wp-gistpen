<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\UsesCustomTable;

/**
 * Class Message
 *
 * @package    Intraxia\Gistpen
 * @subpackage Model
 *
 * @property int    $ID
 * @property int    $run_id
 * @property string $text
 * @property string $level
 * @property string $logged_at
 */
class Message extends Model implements UsesCustomTable {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $fillable = array(
		'run_id',
		'text',
		'level',
		'logged_at',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $visible = array(
		'ID',
		'run_id',
		'text',
		'level',
		'logged_at',
	);

	/**
	 * Returns the custom table name used by the model.
	 *
	 * @return string
	 */
	public static function get_table_name() {
		return 'messages';
	}

	/**
	 * Get the attribute used as the primary key.
	 *
	 * @return string
	 */
	public static function get_primary_key() {
		return 'ID';
	}
}
