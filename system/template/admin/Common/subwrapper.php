<div class="inner tabs tabs-left">
	<ul>
		<?=$_->subrequest($_->vars['menu']);?>
	</ul>
	<div id="<?=$_->vars['this']->toId();?>">
		<? $_->body()?>
	</div>
</div>