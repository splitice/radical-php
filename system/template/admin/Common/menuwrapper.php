<div class="outer tabs">
	<ul>
		<?=$_->subrequest($_->vars['menu']);?>
	</ul>
	<?php 
	if(isset($_->vars['this'])){
	?>
		<div id="<?=$_->vars['this']->toId()?>">
			<?
			$_POST['_admin'] = 'outer';
			echo $_->subrequest($_->vars['this']);
			?>
		</div>
	<?php 
	}
	?>
</div>