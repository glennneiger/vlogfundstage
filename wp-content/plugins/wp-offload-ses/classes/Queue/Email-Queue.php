<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\WP_Queue\Queue;
use DeliciousBrains\WP_Offload_SES\Queue\Jobs\Email_Job;
use DeliciousBrains\WP_Offload_SES\Queue\Connection;

/**
 * Class Email_Queue
 *
 * @since 1.0.0
 */
class Email_Queue extends Queue {

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	protected $connection;

	/**
	 * The delay before running a cron.
	 *
	 * @var int
	 */
	private $delay = 0;

	/**
	 * The number of times to attempt a job.
	 *
	 * @var int
	 */
	private $cron_attempts = 3;

	/**
	 * The minimum time between cron jobs.
	 *
	 * @var int
	 */
	private $cron_interval = 1;

	/**
	 * The table to store jobs.
	 *
	 * @var string
	 */
	private $jobs_table = 'oses_jobs';

	/**
	 * The table to store failures.
	 *
	 * @var string
	 */
	private $failures_table = 'oses_failures';

	/**
	 * Queue constructor
	 */
	public function __construct() {
		global $wpdb;

		$this->connection = new Connection( $wpdb );
		parent::__construct( $this->connection );

		$this->add_cron();
	}

	/**
	 * Add an email to the queue
	 *
	 * @param int $email_id The ID of the email to process.
	 */
	public function process_email( $email_id ) {
		$this->push( new Email_Job( $email_id ), $this->delay );
	}

	/**
	 * Set up the cron for the queue
	 */
	public function add_cron() {
		$this->cron( $this->cron_attempts, $this->cron_interval );
	}

	/**
	 * Use our custom worker
	 *
	 * @param int $attempts The number of times to attempt the job.
	 *
	 * @return Worker
	 */
	public function worker( $attempts ) {
		return new Worker( $this->connection, $attempts );
	}

	/**
	 * Return the number of jobs.
	 *
	 * @return int
	 */
	public function get_total_jobs() {
		return $this->connection->jobs();
	}

	/**
	 * Return the number of failures.
	 *
	 * @return int
	 */
	public function get_total_failures() {
		return $this->connection->failed_jobs();
	}

	/**
	 * Create the database tables if necessary
	 */
	public function install_tables() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$wpdb->hide_errors();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->base_prefix}{$this->jobs_table} (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				job longtext NOT NULL,
				attempts tinyint(3) NOT NULL DEFAULT 0,
				reserved_at datetime DEFAULT NULL,
				available_at datetime NOT NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->base_prefix}{$this->failures_table} (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				job longtext NOT NULL,
				error text DEFAULT NULL,
				failed_at datetime NOT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";
		dbDelta( $sql );
	}

}
