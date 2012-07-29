<?php 
foreach($_->vars['modules'] as $module){
	echo '<li';
	if((string)$module == $_->vars['selected']){
		echo ' class="ui-tabs-selected"';
	}
	echo '><a href="';
	if((string)$module == $_->vars['selected']){
		echo '#'.$module->toId();
	}else{
		echo $_->u($module);
	}
	echo '"';
	if((string)$module == $_->vars['selected']){
		echo ' onclick="window.location=\''.addslashes($_->u($module)).'\'"';
	}
	echo ' title="'.$module->toId().'">',$module,'</a></li>';
}
?>