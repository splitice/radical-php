<?php 
foreach($_->vars['modules'] as $module){
	echo '<li';
	if((string)$module == $_->vars['selected']){
		echo ' class="ui-tabs-selected"';
	}
	echo '><a href="',$_->u($module),'" title="'.$module->toId().'">',$module,'</a></li>';
}
?>