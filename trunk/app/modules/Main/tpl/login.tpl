<form method="POST">


<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg'] == "success" ) : ?>
	<p>Your application was sent successfully. We will email you within 24 hours about our decision.</p>
<?php endif; ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
	<td valign="top" class="login-left" width="520">


	<h1><?=Conf::read('ENV.CompanyName');?> Affiliates Program</h1>
	<p>If you think <?=Conf::read('ENV.CompanyName');?> is a great product and you know others will want to use it to manage their affiliates and track their advertisements, then promote <?=Conf::read('ENV.CompanyName');?> by joining the <?=Conf::read('ENV.CompanyName');?> Affiliates program. Start by <a href="/signup">signing up</a> for your free affiliate account.</p>


	

	<h2>How can you earn money?</h2>
	<img src="/images/p-icons.png" alt="Email, Banners, Facebook, Twitter, and LinkedIn" width="520" />
	</td><td width="20">&nbsp;</td>
	<td valign="top" class="login-right">
	
		<h2>Login</h2>
		
<?php if ( !empty($msg) ) : ?>
	<p><?=$msg;?></p>
<?php endif; ?>

		<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
		<td><div class="logintext">
	Email: </div></td><td>
	<input type="text" name="email" value="<?=isset($_POST['email']) ? $_POST['email'] : '' ?>" class="input-small" />
</td></tr><tr><td><div class="logintext">
	Password:
</div></td><td>
	<input type="password" name="password" value="<?=isset($_POST['password']) ? $_POST['password'] : '' ?>" class="input-small" />
</td></tr></table>

<p><input type="submit" value="" class="login-btn" /></p>

	<h2>Become an Affiliate</h2>
	<p><a href="/signup"><b>Sign up as an affiliate</b></a>.</p>
	
	</td>

</tr></table>
<br/><br/><br/><br/><br/>
</form>