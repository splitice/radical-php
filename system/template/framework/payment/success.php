<h3>Thank you for your order</h3>
<p>You will now be redirected back to the site now. 
<a href="<?=$_->u($_->vars['url']);?>">Click here if it doesnt happen automatically.</a>
</p>
<script>
	setTimeout("location.href = \'<?=addslashes($_->u($_->vars['url']));?>\';",1000);
</script>