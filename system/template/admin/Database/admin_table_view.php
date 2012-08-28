<?php 
if(!function_exists('UU')){
	function uU($url){
		$base = \Utility\Net\URL::fromRequest();
		$base->getPath()->setQuery(array());
		$base = (string)$base.$url;
		return $base;
	}
}
?>
<div class="ui-header ui-bar-b">
	<h1 class="ui-title">Administration</h1>
	<a href="/" data-transition="fade" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-left ui-btn ui-btn-icon-notext ui-btn-corner-all ui-shadow ui-btn-up-b" title="Home" data-theme="b"><span class="ui-btn-inner ui-btn-corner-all" aria-hidden="true"><span class="ui-btn-text">Home</span><span class="ui-icon ui-icon-home ui-icon-shadow"></span></span></a>
</div>

<?php 
	echo '<table class="admin" width="100%"><thead><tr>';
	foreach($_->vars['cols'] as $v=>$col){
		echo '<th>',$v,'</th>';
	}
	echo '<th><a href="'.UU('?action=add').'">Add</a></th>';
	echo '</tr></thead><tbody>';
	
	foreach($_->vars['data'] as $v){
		$v->toSQL();
		
		echo '<tr>';

		foreach($_->vars['cols'] as $colName=>$col){
			echo '<td>',$v->getSQLField($col->getName(),true),'</td>';
		}
		
		//Actions
		echo '<td>';
		echo '<a href="'.UU('?action=edit&id='.urlencode(serialize($v->getIdentifyingSQL()))).'">Edit</a> | ';
		echo '<a href="'.UU('?action=delete&id='.urlencode(serialize($v->getIdentifyingSQL()))).'">Delete</a>';
		echo '</td>';
		
		echo '</tr>';
	}
	echo '</tbody></table>';
	echo '<br />';
	echo '<div class="nodata">';
	$_->vars['pagination']->Output($_->vars['count'], new \Utility\Net\URL\Pagination\Template\Standard());
	echo '</div>';
?>