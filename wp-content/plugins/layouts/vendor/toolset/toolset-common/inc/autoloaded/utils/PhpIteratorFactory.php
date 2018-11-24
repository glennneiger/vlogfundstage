<?php

namespace OTGS\Toolset\Common\Utils;

/**
 * Allow recursively iterating through PHP files inside a given directory.
 *
 * Sorry about two classes in one file, but this is a very specific use case, better to keep them together.
 *
 * @since 3.0.7
 */
class PhpIteratorFactory {

	/**
	 * @param string $path
	 * @return \RegexIterator
	 */
	public function create( $path ) {
		$directory_iterator = new \RecursiveDirectoryIterator( $path );
		$directory_iterator->setFlags( \FilesystemIterator::SKIP_DOTS );

		$dot_iterator = new RecursiveDotFilterIterator( $directory_iterator );

		$recursive_iterator = new \RecursiveIteratorIterator( $dot_iterator );
		$php_files_iterator = new \RegexIterator(
			$recursive_iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH
		);

		return $php_files_iterator;
	}

}

/**
 * Iterator that skips hidden files (starting with a dot).
 */
class RecursiveDotFilterIterator extends \RecursiveFilterIterator {

	public function accept()
	{
		return '.' !== substr($this->current()->getFilename(), 0, 1);
	}

}