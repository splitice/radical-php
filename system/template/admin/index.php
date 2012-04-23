<?php 
echo '<h1>Admin Panel</h1>';
echo '<ul>';
foreach($_->vars['modules'] as $module){
	echo '<li><a href="',$_->u($module),'">',$module,'</a></li>';
}
echo '</ul>';
?>