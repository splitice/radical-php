<?php 
\Web\Resource::add('jQuery');
\Web\Resource::add('jQueryUI');
\Web\Resource::add('admin/radical.php.admin.core');
?>
<!DOCTYPE html>
<html lang="en">
<? $_->incl('Common/html_head','admin'); ?>
<body>
	<div class="tabs">
	<ul>
		<?=$_->subrequest(new \Web\Page\Admin\Menu());?>
	</ul>
	<div id="test">
	<? $_->body()?>
	</div>
</div>
<? $_->inc('Common/footer','framework'); ?>
</body>
</html>