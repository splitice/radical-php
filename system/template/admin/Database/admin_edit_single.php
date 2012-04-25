<div class="ui-header ui-bar-b">
	<h1 class="ui-title">Administration</h1>
</div>

<div class="admin">
	<?php 
	$_->vars['form']->Add(new \HTML\Form\Element\SubmitButton());
	$_->vars['form']->action($_SERVER['REQUEST_URI']);
	//die(var_dump($_->vars['form']));
	echo $_->vars['form'];
	?>
</div>

<?php 
if($_->vars['relations'] && false){
	?>
	<h2>Add Related</h2>
	<ul data-role="listview" data-inset="true" data-theme="d" class="dates" data-split-icon="add" data-split-theme="b">
	<?php 
	foreach($_->vars['relations'] as $relation){
		$table = array_pop(explode('\\',$relation->getReference()->getTableClass()));
		if($table){
	?>
		<li>
			<a href="/admin/<?=$table?>?action=add" data-transition="fade">
				<?=$table?>
			</a>
		</li>
	<?php 
		}
	}
	?>
	</ul>
	<?php
}