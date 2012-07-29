<?php 
\Web\Resource::add('jQuery');
//\Web\Resource::add('ui/jquery.ui.tabs');
\Web\Resource::add('admin/radical.php.admin.core');
?>
<!DOCTYPE html>
<html lang="en">
<? $_->incl('Common/html_head','admin'); ?>
<body>
<? $_->inc('Common/menuwrapper','admin'); ?>
<? $_->inc('Common/footer','framework'); ?>
</body>
</html>