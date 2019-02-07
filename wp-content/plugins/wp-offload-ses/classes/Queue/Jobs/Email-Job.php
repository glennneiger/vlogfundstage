<?php
/**
 * Email job that is ran via cron.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES\Queue\Jobs;

use DeliciousBrains\WP_Offload_SES\WP_Queue\Job;
use DeliciousBrains\WP_Offload_SES\SES_API;
use DeliciousBrains\WP_Offload_SES\Email;
use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;

/**
 * Class Email_Job
 *
 * @since 1.0.0
 */
class Email_Job extends Job {

	/**
	 * The ID of the email to send.
	 *
	 * @var int
	 */
	public $email_id;

	/**
	 * Pass any necessary data to the job.
	 *
	 * @param int $email_id The ID of the email to send.
	 */
	public function __construct( $email_id ) {
		$this->email_id = $email_id;
	}

	/**
	 * Handle the job logic.
	 */
	public function handle() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$atts        = $wp_offload_ses->get_email_log()->get_email( $this->email_id );
		$to          = isset( $atts['email_to'] ) ? $atts['email_to'] : '';
		$subject     = isset( $atts['email_subject'] ) ? $atts['email_subject'] : '';
		$message     = isset( $atts['email_message'] ) ? $atts['email_message'] : '';
		$headers     = isset( $atts['email_headers'] ) ? $atts['email_headers'] : '';
		$attachments = isset( $atts['email_attachments'] ) ? $atts['email_attachments'] : array();
		$client      = $wp_offload_ses->get_ses_api()->get_client();
		$email       = new Email( $to, $subject, $message, $headers, $attachments );
		$raw         = $email->prepare( $this->email_id );
		$data        = array(
			'x-message-id' => $this->id(),
			'RawMessage'   => array(
				'Data' => $raw,
			),
		);

		return $client->getCommand( 'SendRawEmail', $data );
	}

}
