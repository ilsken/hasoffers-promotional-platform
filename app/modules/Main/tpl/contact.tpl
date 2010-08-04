
<h1>Contact</h1>

<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
	<td valign="top">

<?php if( $sent === true ) : ?>
	<p>Email sent successfully.</p>
<?php elseif( $sent === false ) : ?>
	<p>Email could not be sent. Try again later.</p>
<?php endif; ?>

		<form method="POST" name="form1">
		<p>Need help promoting <?=Conf::read('ENV.CompanyName');?> another way? Send us a suggestion.</p>
		<p>Your name: <input type="text" name="name" value="<?=$name;?>"></p>
		<p>Your email: <input type="text" name="email" value="<?=$email;?>"></p>
		<p>Subject:  <input type="text" name="subject" value="<?=$subject;?>"></p>
		<p>Message: <textarea name="message"><?=$message;?></textarea></p>
		<a href="#" onclick="validate();">Send</a>
		</form>
	</td><td valign="top">
		<h3>Account Manager</h3>
		<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
			<td valign="top"><img src="http://t1.gstatic.com/images?q=tbn:ANd9GcSxj8oI7uce1g9fMgR-WkKCaK8Gja4hq3ZbUq6CiFgnQOW6Sq4&t=1&usg=__GusXjAS-g50ebO_RvULl6qW4ywE=" border="0" width="80"/></td>
			<td valign="top">
				<p style="margin-top: 0px;margin-left:15px;"><b>Megan Fox</b><br/>
				Vice President, Marketing
				Email: megan@hasoffers.com<br/>
				Phone: 206-508-1318<br/>
				AIM: hasoffers<br/>
				Twitter: @hasoffers<br/>
				<a href="">LinkedIn</a>
				</p>
			</td>
		</tr></table>
	</td>
</tr></table>

