<?php
foreach($_->vars['libraries'] as $libName=>$info){
	echo '<h2>'.$libName.'</h2>';
	echo '<p>';
	echo '<b>Files: </b>'.$info['files'].'<br />';
	echo '<b>Lines: </b>'.$info['lines'].'<br />';
	echo '</p>';
}