<?php 
$submodules = $_->vars['module']->getSubmodules();

foreach($submodules as $s){
	echo '<li';
	if((string)$s == $_->vars['selected']){
		echo ' class="ui-tabs-selected"';
	}
	echo '><a href="',$_->u($s),'" title="'.$s->toId().'">',$s,'</a></li>';
}