<?php
namespace Web\Form\Builder;

interface IFormModifier {
	function action($action);
	function method($method);
}