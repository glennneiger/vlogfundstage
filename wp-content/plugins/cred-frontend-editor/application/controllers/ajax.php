<?php

/**
 * Main Class responsible to set CRED Ajax handlers
 *
 * @since 1.9.4
 */
class CRED_Ajax {

	public function initialize() {
		(new CRED_Ajax_Media_Upload_Fix())->initialize();
		(new CRED_Form_Ajax_Init())->initialize();
	}
}