
<form method="POST">

<h1>Display with Banners</h1>

<div id="pagelogo"><img src="/images/p-banners.png" alt="banners" /></div>

<?php if(!empty($banners)) { ?>


<ol>
	<li>Choose one or more of the banners to place on your website or blog</li>
	<li>Copy the code for the banner from the textbox</li>
	<li>Place the code in the source code of your website or blog</li>
	<li>Save your changes and preview the banner you just placed</li>
	<li>Earn money displaying <?=Conf::read('ENV.CompanyName');?> banners</li>
</ol>
<?php foreach ($banners as $banner) { ?>
	<p><b><?=$banner['OfferFile']['display']?></b> - <a href="javascript:showCreativeCode( <?=$banner['OfferFile']['id']?> )">Get Creative Code</a><br/>
		<iframe src="/view_creative?id=<?=$banner['OfferFile']['id']?>" height="<?=$banner['OfferFile']['height']?>" width="<?=$banner['OfferFile']['width']?>" frameborder="0" style="margin: 0px 0px 0px 0px;border: 1px solid #cccccc;background: white;" scrolling="no"></iframe><br/>
		<a href="javascript:showCreativeCode( <?=$banner['OfferFile']['id']?> )"><b>Get Creative Code</b></a><br/>
		<div id="creativeCode<?=$banner['OfferFile']['id']?>"></div> 
	</p>
<?php } ?>

<?php } else { ?>
	<p>There are no banners available currently.</p>
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
