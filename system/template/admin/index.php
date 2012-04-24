<?php 
echo '<h1>Admin Panel</h1>';
echo '<ul>';
foreach($_->vars['modules'] as $module){
	echo '<li><a href="',$_->u($module),'">',$module,'</a>';
	$submodules = $module->getSubmodules();
	if($submodules){
		echo '<ul>';
		foreach($submodules as $sub){
			echo '<li><a href="',$_->u($sub),'">',$sub,'</a>';
		}
		echo '</ul>';
	}
	echo '</li>';
}
echo '</ul>';
?>