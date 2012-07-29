<div class="inner tabs tabs-left">
	<ul>
		<?=$_->subrequest(new \Web\Page\Admin\SubMenu(new \Web\Page\Admin\Modules\Database()));?>
	</ul>
	<div id="tt">
		<? $_->body()?>
	</div>
</div>