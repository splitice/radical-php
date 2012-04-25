<div class="ui-header ui-bar-b">
	<h1 class="ui-title">Administration</h1>
	<a href="/" data-transition="fade" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-left ui-btn ui-btn-icon-notext ui-btn-corner-all ui-shadow ui-btn-up-b" title="Home" data-theme="b"><span class="ui-btn-inner ui-btn-corner-all" aria-hidden="true"><span class="ui-btn-text">Home</span><span class="ui-icon ui-icon-home ui-icon-shadow"></span></span></a>
</div>

<ul data-role="listview" data-inset="true" data-dividertheme="d">
<?php 
	foreach($_->vars['classes'] as $v=>$url){
		echo '<li><a href="'.$url.'">'.$v.'</a></li>';
	}
?>
</ul>