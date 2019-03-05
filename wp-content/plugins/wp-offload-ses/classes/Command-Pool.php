<?php
/**
 * Wrapper for the AWS command pool.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\CommandPool;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\ResultInterface;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\Ses\Exception\SesException;
use DeliciousBrains\WP_Offload_SES\Aws3\GuzzleHttp\Promise\PromiseInterface;
use DeliciousBrains\WP_Offload_SES\Queue\Connection;

/**
 * Class Command_Pool
 *
 * @since 1.0.0
 */
class Command_Pool {

	/**
	 * The number of times to attempt a job.
	 *
	 * @var int
	 */
	private $attempts;

	/**
	 * The commands to send via the AWS CommandPool.
	 *
	 * @var array
	 */
	private $commands = array();

	/**
	 * The maximum concurreny for the AWS CommandPool.
	 *
	 * @var int
	 */
	private $concurrency;

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	private $connection;

	/**
	 * Construct the Command_Pool class.
	 *
	 * @param Connection $connection The database connection.
	 * @param int        $attempts   The number of times to attempt a job.
	 */
	public function __construct( $connection, $attempts ) {
		$this->connection = $connection;
		$this->attempts   = $attempts;
	}

	/**
	 * Add a command to be ran via the CommandPool.
	 *
	 * @param string $command The command to add.
	 */
	public function add_command( $command ) {
		$this->commands[] = $command;
		$num_commands     = count( $this->commands );

		if ( $this->get_concurrency() === $num_commands || $this->connection->jobs() === $num_commands ) {
			$this->execute();
			$this->commands = array();
		}
	}

	/**
	 * Get the maximum number of requests to be sent at once.
	 *
	 * @return int
	 */
	private function get_concurrency() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		if ( ! is_null( $this->concurrency ) ) {
			return $this->concurrency;
		}

		$quota     = $wp_offload_ses->get_ses_api()->get_send_quota();
		$send_rate = 10;

		if ( ! is_wp_error( $quota ) ) {
			$send_rate = $quota['rate'];
		}

		$this->concurrency = (int) apply_filters( 'wposes_max_concurrency', $send_rate );

		return $this->concurrency;
	}

	/**
	 * Create the command pool and execute the commands.
	 */
	private function execute() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		// Initiate the command pool.
		$client       = $wp_offload_ses->get_ses_api()->get_client();
		$command_pool = new CommandPool( $client, $this->commands, [
			'concurrency' => $this->get_concurrency(),
			'fulfilled' => function (
				ResultInterface $result,
				$iterKey,
				PromiseInterface $aggregatePromise
			) {
				/** @var WP_Offload_SES $wp_offload_ses */
				global $wp_offload_ses;

				$id  = $this->commands[ $iterKey ]['x-message-id'];
				$job = $this->connection->get_job( $id );

				$this->connection->delete( $job );
				$wp_offload_ses->get_email_log()->update_email( $job->email_id, 'email_status', 'sent' );
			},
			'rejected' => function (
				SesException $reason,
				$iterKey,
				PromiseInterface $aggregatePromise
			) {
				/** @var WP_Offload_SES $wp_offload_ses */
				global $wp_offload_ses;

				$id  = $this->commands[ $iterKey ]['x-message-id'];
				$job = $this->connection->get_job( $id );

				$job->release();
				$wp_offload_ses->get_email_events()->delete_links_by_email( $job->email_id );

				if ( $job->attempts() >= $this->attempts ) {
					$job->fail();
				}

				if ( $job->failed() ) {
					$this->connection->failure( $job, $reason );
					$wp_offload_ses->get_email_log()->update_email( $job->email_id, 'email_status', 'failed' );
				} else {
					$this->connection->release( $job );
				}
			},
		]);

		// Send the emails in the pool.
		$promise = $command_pool->promise();
		$promise->wait();
	}

}