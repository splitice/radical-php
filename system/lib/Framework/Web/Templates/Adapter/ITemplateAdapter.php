<?php
namespace Web\Templates\Adapter;

use Web\Templates\Scope;

/**
 * Different template systems are supported through
 * implementing this interface.
 * 
 * @author SplitIce
 *
 */
interface ITemplateAdapter {
	/**
	 * Adapters will be instantized with the file
	 * they will be handling.
	 * 
	 * @param \File $file file to render
	 */
	function __construct(\File $file);
	
	/**
	 * Render/Output the template.
	 * 
	 * @param Scope $_ the scope (variables, helper functions)
	 */
	function output(Scope $_);
	
	/**
	 * Does this adapter know how to handle the passed in type
	 * of file? Returning true is a promise to handle this file.
	 * 
	 * @param \File $file file to process
	 */
	static function is(\File $file);
}