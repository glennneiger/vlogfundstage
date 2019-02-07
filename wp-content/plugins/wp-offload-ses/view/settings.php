<?php
/* @var \WPOSES $this */

if ( $this->is_plugin_setup() ) {
	$this->render_view( 'tabs/reports' );
	$this->render_view( 'tabs/settings/general' );
	$this->render_view( 'tabs/settings/verified-senders' );
	$this->render_view( 'tabs/settings/send-test-email' );
	$this->render_view( 'tabs/settings/aws-access-keys' );
	$this->render_view( 'tabs/settings/licence' );
} else {
	$this->render_view( 'setup/start' );
	$this->render_view( 'setup/create-iam-user' );
	$this->render_view( 'setup/access-keys' );
	$this->render_view( 'setup/sandbox-mode' );
	$this->render_view( 'setup/verify-sender' );
	$this->render_view( 'setup/complete-verification' );
	$this->render_view( 'setup/configure-wp-offload-ses' );
}

$this->render_view( 'tabs/support' );

do_action( 'wposes_after_settings' );

// $this->render_view( 'sidebar' );
?>