<?php
namespace Basic\Validation;

interface IValidator {
	/**
	 * Perform validation.
	 * 
	 * Returns true if is valid, false otherwise.
	 * 
	 * @param mixed $value
	 */
	function validate($value);
}