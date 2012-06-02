<!DOCTYPE html>
<html lang="en">
<? $_->incl('Common/html_head','admin'); ?>
<body>
	<!-- Start: page-top-outer -->
	<div id="page-top-outer">

		<!-- Start: page-top -->
		<div id="page-top">

			<!-- start logo -->
			<div id="logo">
				<a href=""><img src="images/shared/logo.png" width="156" height="40"
					alt="" /></a>
			</div>
			<!-- end logo -->

			<!--  start top-search -->
			<div id="top-search">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><input type="text" value="Search"
							onblur="if (this.value=='') { this.value='Search'; }"
							onfocus="if (this.value=='Search') { this.value=''; }"
							class="top-search-inp" /></td>
						<td><select class="styledselect">
								<option value="">All</option>
								<option value="">Products</option>
								<option value="">Categories</option>
								<option value="">Clients</option>
								<option value="">News</option>
						</select></td>
						<td><input type="image" src="images/shared/top_search_btn.gif" />
						</td>
					</tr>
				</table>
			</div>
			<!--  end top-search -->
			<div class="clear"></div>

		</div>
		<!-- End: page-top -->

	</div>
	<!-- End: page-top-outer -->

	<div class="clear">&nbsp;</div>

	<?=$_->subrequest(new \Web\Page\Admin\Menu());?>

	<div class="clear"></div>

	<!-- start content-outer ........................................................................................................................START -->
	<div id="content-outer">
		<!-- start content -->
		<div id="content">

			<!--  start page-heading -->
			<div id="page-heading">
				<h1>Add product</h1>
			</div>
			<!-- end page-heading -->

	<? $_->body()?>
	
	<div class="clear">&nbsp;</div>

		</div>
		<!--  end content -->
		<div class="clear">&nbsp;</div>
	</div>
	<!--  end content-outer........................................................END -->

	<div class="clear">&nbsp;</div>

	<!-- start footer -->
	<div id="footer">
		<!--  start footer-left -->
		<div id="footer-left">

			Admin Skin &copy; Copyright Internet Dreams Ltd. <span id="spanYear"></span>
			<a href="">www.netdreams.co.uk</a>. All rights reserved.
		</div>
		<!--  end footer-left -->
		<div class="clear">&nbsp;</div>
	</div>
	<!-- end footer -->
</body>
</html>