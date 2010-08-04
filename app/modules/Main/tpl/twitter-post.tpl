<h2>Tweet This</h2>

<div id="pagelogo"><img src="/images/logo-twitter.gif" border="0" alt="Twitter" /></div>

<?php if( $sent === true ) : ?>
	<p>Tweet successfully posted to <a href="http://www.twitter.com/<?=$username;?>" target="_blank">http://www.twitter.com/<?=$username;?></a>.</p>
<?php elseif( $sent === false ) : ?>
	<p>Could not post Tweet to Twitter right now. Try again later.</p>
<?php endif; ?>

<form method="POST" name="form1" id="form1">

<p>Your Twitter Username: <input type="text" name="username" value="<?=$username;?>"/></p>
<p>Your Twitter Password: <input type="password" name="password" value="<?=$password;?>"/></p>
<p>Confirm Your Password: <input type="password" name="confirm_password" value="<?=$password;?>" onKeyUp="checkPass();"/>
	<span id="checkyes" style="display: none;color: #009966;">passwords match</span><span id="checkno" style="display: none;color:#990000;">passwords don't match</span></p>

<p>Confirm your Tweet below:</p>
<textarea name="status" class="textboxbig" maxlength="140"><?=$status;?></textarea> 

<p><a href="#" onclick="validate();"><img src="/images/button-tweet.gif" border="0" alt="Tweet This"/></a></p>
	
</form>
