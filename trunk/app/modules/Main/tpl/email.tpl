
<form method="POST">

<h1>Send via Email</h1>

<div id="pagelogo"><img src="/images/p-email.png" alt="email" /></div>

<?php if(!empty($emails)) { ?>

<ol>
	<li>Choose one or more of the email creatives to send</li>
	<li>Copy the code and paste it into your email client</li>
	<li>Send to as many contacts you have permission for</li>
	<li>Download DNE suppression list at the bottom of bulk mailings</li>
	<li>Earn money emailing contacts to signup to <?=Conf::read('ENV.CompanyName');?></li>
</ol>

<?php foreach ($emails as $email) { ?>
	<p><b><?=$email['OfferFile']['display']?></b> - <a href="javascript:showCreativeCode( <?=$email['OfferFile']['id']?> )">Get Creative Code</a><br/>
		<iframe src="/view_creative?id=<?=$email['OfferFile']['id']?>" height="300" width="500" frameborder="0" style="margin: 0px 0px 0px 0px;border: 1px solid #cccccc;background: white;" scrolling="no"></iframe><br/>
		<a href="javascript:showCreativeCode( <?=$email['OfferFile']['id']?> )"><b>Get Creative Code</b></a><br/>
		<div id="creativeCode<?=$email['OfferFile']['id']?>"></div> 
	</p>
<?php } ?>

<?php if(!empty($dne_list)) { ?>
	<p><b>Download DNE suppression list</b>: <a href="<?php echo $dne_list['DneList']['url']; ?>" target="_blank"><?php echo $dne_list['DneList']['url']; ?></a></p>
	<p><b>DNE unsubscribe link</b>: <a href="<?php echo $dne_list['DneList']['unsubscribe_link']; ?>" target="_blank"><?php echo $dne_list['DneList']['unsubscribe_link']; ?></a></p>
<?php } ?>


<?php } else { ?>
	<p>There are no email creatives available currently.</p>
<?php } ?>

</form>


<script type="text/javascript">
//<![CDATA[
function showCreativeCode( id ) {
	$('#creativeCode'+id).load(
		// Here is the tricky part. Instead of hard-coding a url to pass, I just had jquery
		// go look at what the link (from the outside scope, .click() part) was already going
		// to (href) and used that as the argument.
		'show_creative_code?id='+id
			, function () {
				$(this).show();
			}
	);
}
//]]>
</script>	