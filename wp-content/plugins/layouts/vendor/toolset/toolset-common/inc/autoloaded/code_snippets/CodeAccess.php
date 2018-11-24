<?php

namespace OTGS\Toolset\Common\CodeSnippets;

/**
 * Reads and parses the code of the snippet to obtain additional information, and saves updated snippet code.
 *
 * @since 3.0.8
 */
class CodeAccess {


	/** @var Snippet */
	private $snippet;

	/** @var \Toolset_Files */
	private $files;


	/**
	 * CodeAccess constructor.
	 *
	 * @param Snippet $snippet
	 * @param \Toolset_Files $files
	 */
	public function __construct( Snippet $snippet, \Toolset_Files $files ) {
		$this->snippet = $snippet;
		$this->files = $files;
	}


	/**
	 * Decorate the snippet model with its source code.
	 *
	 * Parse the source code and try to extract a first docblock. If one is present, set its sanitized content as
	 * the snippet's description.
	 */
	public function decorate_snippet() {
		if( ! $this->files->is_file( $this->snippet->get_absolute_file_path() ) ) {
			return;
		}

		$snippet_code = $this->files->file_get_contents( $this->snippet->get_absolute_file_path() );
		if ( false === $snippet_code ) {
			return;
		}

		$this->snippet->set_code( $snippet_code );

		$docblocks = array_filter( token_get_all( $snippet_code ), function ( $token ) {
			return $token[0] === T_DOC_COMMENT;
		} );

		if ( empty( $docblocks ) ) {
			return;
		}

		// Get content of the first docblock token.
		$description_token = reset( $docblocks );
		$description = toolset_getarr( $description_token, 1 );

		// Sanitize the description.
		$description = str_replace( '/**', '', $description );
		$description = str_replace( '*/', '', $description );
		$description = str_replace( '* ', '', $description );

		// Get rid of whitespace around each line, and ensure empty lines are empty (where '*' wasn't matched by the replacement above).
		$description = implode(
			"\n",
			array_map(
				function ( $line ) {
					return ( '*' === $line ? '' : $line );
				},
				array_filter(
					array_map( 'trim', explode( "\n", $description ) ),
					function ( $line ) {
						return ! empty( $line );
					}
				)
			)
		);

		$description = sanitize_textarea_field( $description );
		$description = wp_trim_words( $description, 55, '...' );

		$this->snippet->set_description( $description );
	}


	/**
	 * Store new code in the snippet file.
	 *
	 * Also updates the snippet model if the file has been successfully updated.
	 *
	 * @param string $code
	 *
	 * @return bool True if the file has been updated.
	 */
	public function update_snippet_code( $code ) {
		$result = $this->files->file_put_contents( $this->snippet->get_absolute_file_path(), $code );

		$is_success = ( $result !== false );

		if( $is_success ) {
			$this->snippet->set_code( $code );
		}

		return $is_success;
	}
}