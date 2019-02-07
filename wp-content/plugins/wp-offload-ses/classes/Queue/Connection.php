<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\WP_Queue\Connections\DatabaseConnection;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Job;

/**
 * Class Connection
 *
 * @since 1.0.0
 */
class Connection extends DatabaseConnection {

	/**
	 * Table to store jobs.
	 *
	 * @var string
	 */
	protected $jobs_table;

	/**
	 * Table to store failures.
	 *
	 * @var string
	 */
	protected $failures_table;

	/**
	 * Construct the Connection class.
	 *
	 * @param \wpdb $wpdb WordPress database class.
	 */
	public function __construct( $wpdb ) {
		parent::__construct( $wpdb );

		$this->jobs_table     = $this->database->base_prefix . 'oses_jobs';
		$this->failures_table = $this->database->base_prefix . 'oses_failures';
	}

	/**
	 * Retrieve a job by ID
	 *
	 * @param int $id The ID of the job to retrieve.
	 *
	 * @return bool|Job
	 */
	public function get_job( $id ) {
		$sql     = $this->database->prepare( "SELECT * FROM {$this->jobs_table} WHERE id = %d", $id );
		$raw_job = $this->database->get_row( $sql );

		if ( is_null( $raw_job ) ) {
			return false;
		}

		return $this->vitalize_job( $raw_job );
	}

}
