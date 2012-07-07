<?php 
foreach($_->vars['modules'] as $module){
	echo '<li><a href="',$_->u($module),'">',$module,'</a></li>';
}
?>