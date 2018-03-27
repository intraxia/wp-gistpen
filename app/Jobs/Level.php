<?php
namespace Intraxia\Gistpen\Jobs;

use MyCLabs\Enum\Enum;

class Level extends Enum {
	const ERROR = 'error';
	const WARNING = 'warning';
	const SUCCESS = 'success';
	const INFO = 'info';
	const DEBUG = 'debug';
}
