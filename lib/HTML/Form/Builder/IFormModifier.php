<?php
namespace HTML\Form\Builder;

interface IFormModifier {
	function action($action);
	function method($method);
}