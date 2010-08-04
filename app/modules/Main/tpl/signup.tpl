<h1>Affiliate Signup</h1>

<? if (Filter::hasVal('leadSignup')) : ?>
<script type="text/javascript">
        $().ready(function(){
        $('#account_div').fadeIn('slow');
    });
</script>
<? endif; ?>

<form method="POST" name="form1">

<?php if( !empty($msg) ) : ?>
	<p><?=$msg;?></p>
<?php endif; ?>

<div id="user_div">
<h2>User Details</h2>

<p>First name: <input type="text" name="first_name" value="<?=$first_name;?>"/></p>
<p>Last name: <input type="text" name="last_name" value="<?=$last_name;?>"/></p>
<p>Email: <input type="text" name="email" value="<?=$email;?>"/></p>
<p>Confirm Email: <input type="text" name="confirm_email" onKeyUp="checkEmail();" value="<?=$email;?>"/></p>
	<div id="emailyes" style="display: none;color:#009966;">emails match</div><div id="emailno" style="display: none;color:#990000;">emails don't match</div></td></tr> 
<p>Password: <input type="password" name="password" value="<?=$password;?>"/></p>
<p>Confirm Password: <input type="password" name="confirm_password"value="<?=$password;?>" onKeyUp="checkPass();" onfocus="$('#account_div').fadeIn('slow');changeCompany();" /></p>
	<div id="passyes" style="display: none;color: #009966;">passwords match</div><div id="passno" style="display: none;color:#990000;">passwords don't match</div>
</div>

<div id="account_div" style="<?php if(!isset($msg)) : echo "display: none;"; endif; ?>">
<h2>Account Details</h2>

<p>Company: <input type="text" name="company" value="<?=$company;?>"/></p>
<p>Country: <input type="text" name="country" value="<?=$country;?>" /></p>
<p>Address: <input type="text" name="address1" value="<?=$address1;?>" /></p>
<p>City: <input type="text" name="city" value="<?=$city;?>"/></p>
<p>State: <input type="text" name="region" value="<?=$region;?>"/></p>
<p>Zipcode: <input type="text" name="zipcode" value="<?=$zipcode;?>"/></p>
<p>Phone: <input type="text" name="phone" value="<?=$phone;?>"/></p>

<p><a name="terms"></a><input id="termsconditions" name="terms" value="checked" type="checkbox" <?php if(isset($msg) && $msg == "fail" ) : echo "checked"; endif;?>> 
<a href="#terms" onclick="document.getElementById('termsconditions').checked=true;">Click here</a> to the <a href="http://<?=Conf::read('ApiClient.NetworkId');?>.hasoffers.com/terms" target="_blank">Terms and Conditions</a></p>
</div>

<input type="hidden" name="referral_id" value="<?=$referral_id;?>" />

<input type="button" value="Submit" onclick="validate();"/>

</form>