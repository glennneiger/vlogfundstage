<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\Command_Pool;

/**
 * Class Worker
 *
 * @since 1.0.0
 */
class Worker {

	/**
	 * The AWS Command Pool wrapper.
	 *
	 * @var Command_Pool
	 */
	protected $command_pool;

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	private $connection;

	/**
	 * Worker constructor.
	 *
	 * @param Connection $connection The database connection.
	 * @param int        $attempts   The number of times to attempt a job.
	 */
	public function __construct( $connection, $attempts = 3 ) {
		$this->command_pool = new Command_Pool( $connection, $attempts );
		$this->connection   = $connection;
	}

	/**
	 * Process a job on the queue.
	 *
	 * @return bool
	 */
	public function process() {
		$job = $this->connection->pop();

		if ( ! $job ) {
			return false;
		}

		$this->command_pool->add_command( $job->handle() );

		return true;
	}

}
