<h1>Promote <?=Conf::read('ENV.CompanyName');?></h1>

<p><?=$user['first_name']; ?> your affiliate tracking link for <?=$selectedOffer['Offer']['name'];?> is below. You will be compensated
	<?php 
		$payout_type = $selectedOffer['Offer']['payout_type'];
		$payout_amount = $selectedOffer['Offer']['default_payout'];
		$payout_percentage = $selectedOffer['Offer']['percent_payout'];

		if($payout_type == "cpa_flat") :
			$payout = "$".$payout_amount." per Conversion";
		elseif($payout_type == "cpa_percentage") :
			$payout = $payout_percentage."% of Sale";
		elseif($payout_type == "cpa_both") :
			$payout = "$".$payout_amount." per Conversion + ".$payout_amount."% of Sale";
		elseif($payout_type == "cpc") :
			$payout = "$".$payout_amount." CPC";
		elseif($payout_type == "cpm") :
			$payout = "$".$payout_amount." CPM";
		endif;
		echo $payout;
	?>. Use one of our promotional tools below to recommend <?=Conf::read('ENV.CompanyName');?> to co-workers, friends and other contacts.
</p>

<input type="text" value="<?=$tracking['click_url'];?>" class="inputbig" >

<div class="highhead">
<h2>How do you want to promote <?=Conf::read('ENV.CompanyName');?>?</h2>
</div>
<div class="highlight">
<p>Earning money promoting <?=$selectedOffer['Offer']['name'];?> with <a href="/banners">Banners</a>,  <a href="/email">Email</a>, <a href="/facebook">Facebook</a>, <a href="/linkedin">LinkedIn</a> and <a href="/twitter">Twitter</a>.</p>

<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr>
	<td><a href="/banners"><img src="images/p-banners.png" border="0" height="25"/></a></td>
	<td><a href="/email"><img src="images/p-email.png" border="0" height="25"/></a></td>
	<td><a href="/facebook"><img src="images/logo-facebook.gif" border="0" height="28"/></a></td>
	<td><a href="/linkedin"><img src="images/logo-linkedin.gif" border="0" height="28"/></a></td>
	<td><a href="/twitter"><img src="images/logo-twitter.gif" border="0" height="28"/></a></td>
</tr></table>
</div>

<h2>How do you want to earn money?</h2>

<p>Chose one of the offers below to promote. Once you've selected how you want to earn money, chose from the top navigation on how to promote the selected offer.</p>

<?php foreach ($offers as $offer) : ?>
	<form method="POST" name="form<?=$offer['Offer']['id']?>">
		<h3><a href="#" onclick="document.form<?=$offer['Offer']['id'];?>.submit();"><?=$offer['Offer']['name'];?></a>
		<?php 
		$payout_type = $offer['Offer']['payout_type'];
		$payout_amount = $offer['Offer']['default_payout'];
		$payout_percentage = $offer['Offer']['percent_payout'];

		if($payout_type == "cpa_flat") :
			$payout = "$".$payout_amount." per Conversion";
		elseif($payout_type == "cpa_percentage") :
			$payout = $payout_percentage."% of Sale";
		elseif($payout_type == "cpa_both") :
			$payout = "$".$payout_amount." per Conversion + ".$payout_amount."% of Sale";
		elseif($payout_type == "cpc") :
			$payout = "$".$payout_amount." CPC";
		elseif($payout_type == "cpm") :
			$payout = "$".$payout_amount." CPM";
		endif;
		echo $payout;
		?>
		</h3>
		<p><?=$offer['Offer']['description'];?></p><input type="hidden" name="offer" value="<?=$offer['Offer']['id'];?>" />
	</form>
<?php endforeach; ?>



