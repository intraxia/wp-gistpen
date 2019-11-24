<?php
namespace Intraxia\Gistpen\Jobs;

use MyCLabs\Enum\Enum;

/**
 * Status types.
 */
class Status extends Enum {
	// Job Status
	const IDLE       = 'idle';
	const PROCESSING = 'processing';

	// Run Status
	const SCHEDULED = 'scheduled';
	const RUNNING   = 'running';
	const PAUSED    = 'paused';
	const FINISHED  = 'finished';
	const ERROR     = 'error';
}
