<?php 
$submodules = $_->vars['module']->getSubmodules();
foreach($submodules as $s){
	echo '<li><a href="',$_->u($s),'">',$s,'</a></li>';
}