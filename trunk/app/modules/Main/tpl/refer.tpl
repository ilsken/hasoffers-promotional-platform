<h1>Refer a Friend</h1>

<p>Use the link below to get friends to signup to our affiliate program and make
	<?php if( $refer_commission['AffiliateReferralCommission']['rate_type'] == "flat") : echo "$"; endif; ?><?=$refer_commission['AffiliateReferralCommission']['rate'];?><?php if( $refer_commission['AffiliateReferralCommission']['rate_type'] == "percentage") : echo "%"; endif; ?>
	on their <?=$refer_commission['AffiliateReferralCommission']['field'];?>.</p>
<p><input type="text" value="http://<?=$_SERVER['HTTP_HOST'];?>/signup?r=<?=$user['affiliate_id'];?>" class="inputbig" /></p>

<?php if(empty($refer_count)) : ?>
	<p>You have referred 0 friends so far.</p>
<?php else : ?>
	<p>You have referred <?=$refer_count;?> friends so far.</p>
<?php endif; ?>


<h2>Referral Commission</h2>
<?php if(!empty($refer_stats['data'])) { ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<th>Date</th>
		<th>Earnings</th>
	</tr>
<?php foreach ($refer_stats['data'] as $refer_stat) { ?>
	<tr>
		<td><?=$refer_stat['Stat']['date'];?></td>
		<td>$<?=number_format($refer_stat['Stat']['amount']);?></td>
	</tr>
<?php } ?>
<?php if (!empty($refer_stats['totals'])) { ?>
	<tr>
		<td>Total:</td>
		<td>$<?=number_format($refer_stats['totals']['Stat']['amount']);?></td>
	</tr>
<?php } ?>
</table>

<?php } else { ?>
	<p>You have not generated any conversions yet.</p>
<?php } ?>