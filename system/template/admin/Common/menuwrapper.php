<div class="outer tabs">
	<ul>
		<?=$_->subrequest($_->vars['menu']);?>
	</ul>
	<div id="<?=$_->vars['this']->toId()?>">
		<?
		$_POST['_admin'] = 'outer';
		echo $_->subrequest($_->vars['this']);
		?>
	</div>
</div>