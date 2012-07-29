<?php 
$submodules = $_->vars['module']->getSubmodules();
$_->vars['modules'] = $submodules;
$_->incl('Common/menu','admin');