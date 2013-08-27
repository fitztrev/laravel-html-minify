<?php namespace Fitztrev\LaravelHtmlMinify;

use Illuminate\View\Compilers\BladeCompiler;

class LaravelHtmlMinifyCompiler extends BladeCompiler {

	public function __construct($files, $cachePath) {
		parent::__construct($files, $cachePath);

		// Add Minify to the list of compilers
		$this->compilers[] = 'Minify';
	}

	/**
	* Compress the HTML output before saving it
	*
	* @param  string  $value
	* @return string
	*/
	protected function compileMinify($value)
	{
		// First, check to make sure there are no <pre> or <textarea> blocks inside the view.
		// If there are, we won't bother to compress this view.
		if ( !preg_match('/<(pre|textarea)/', $value) ) {
			// Remove line breaks, carriage returns, and tabs
			$value = str_replace(array("\n", "\r", "\t"), ' ', $value);
			// Replace multiple blank spaces with just one space
			$value = preg_replace('/ +/', ' ', $value);
			// Trim any whitespace from the beginning + end of file
			$value = trim($value);
		}
		return $value;
	}

}
