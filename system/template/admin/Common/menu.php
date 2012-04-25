<!--  start nav-outer-repeat................................................................................................. START -->
<div class="nav-outer-repeat">
	<!--  start nav-outer -->
	<div class="nav-outer">

		<!-- start nav-right -->
		<div id="nav-right">

			<div class="nav-divider">&nbsp;</div>
			<div class="showhide-account">
				<img src="images/shared/nav/nav_myaccount.gif" width="93"
					height="14" alt="" />
			</div>
			<div class="nav-divider">&nbsp;</div>
			<a href="" id="logout"><img src="images/shared/nav/nav_logout.gif"
				width="64" height="14" alt="" /></a>
			<div class="clear">&nbsp;</div>

			<!--  start account-content -->
			<div class="account-content">
				<div class="account-drop-inner">
					<a href="" id="acc-settings">Settings</a>
					<div class="clear">&nbsp;</div>
					<div class="acc-line">&nbsp;</div>
					<a href="" id="acc-details">Personal details </a>
					<div class="clear">&nbsp;</div>
					<div class="acc-line">&nbsp;</div>
					<a href="" id="acc-project">Project details</a>
					<div class="clear">&nbsp;</div>
					<div class="acc-line">&nbsp;</div>
					<a href="" id="acc-inbox">Inbox</a>
					<div class="clear">&nbsp;</div>
					<div class="acc-line">&nbsp;</div>
					<a href="" id="acc-stats">Statistics</a>
				</div>
			</div>
			<!--  end account-content -->

		</div>
		<!-- end nav-right -->


		<!--  start nav -->
		<div class="nav">
			<div class="table">
<?php 
foreach($_->vars['modules'] as $module){
	echo '<ul class="select">';
	echo '<li><a href="',$_->u($module),'"><b>',$module,'</b><!--[if IE 7]><!--></a><!--<![endif]--> <!--[if lte IE 6]><table><tr><td><![endif]-->';
	$submodules = $module->getSubmodules();
	if($submodules){
		echo '<div class="select_sub"><ul class="sub">';
		foreach($submodules as $sub){
			echo '<li><a href="',$_->u($sub),'">',$sub,'</a>';
		}
		echo '</ul></div>';
	}
	echo ' <!--[if lte IE 6]></td></tr></table></a><![endif]--></li>';
	echo '</ul><div class="nav-divider">&nbsp;</div>';
}
?>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
		<!--  start nav -->

	</div>
	<div class="clear"></div>
	<!--  start nav-outer -->
</div>
<!--  start nav-outer-repeat................................................... END -->