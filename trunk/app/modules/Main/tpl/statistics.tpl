<h1>Earnings Report</h1>

<canvas id="myCanvas2" width="760" height="300">[No canvas support]</canvas>

<?php if(!empty($conversions['data'])) { ?>
<h2>Offer Earnings</h2>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<thead><tr>
		<th>Date</th>
		<th>Offer</th>
		<th>Earnings</th>
		<th>Source</th>
	</tr></thead>
<?php foreach ($conversions['data'] as $conversion) { ?>
	<tr>
		<td><?=$conversion['Stat']['date'];?></td>
		<td><?=$conversion['Offer']['name'];?></td>
		<td>$<?=number_format($conversion['Stat']['payout']);?></td>
		<td><?=$conversion['Stat']['source'];?></td>
	</tr>
<?php } ?>
<?php if (!empty($conversions['totals'])) { ?>
	<tr>
		<td>Total:</td>
		<td></td>
		<td>$<?=$conversions['totals']['Stat']['payout'];?></td>
		<td></td>
	</tr>
<?php } ?>
</table>

<?php } else { ?>
	<p>You have not generated any conversions yet.</p>
<?php } ?>



<?php if(!empty($refer_stats['data'])) { ?>
<h2>Refer a Friend Earnings</h2>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<?php foreach ($refer_stats['data'] as $refer_stat) { ?>
	<tr>
		<td>Affiliate Referral Commission</td>
		<td>$<?=number_format($refer_stat['Stat']['amount']);?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>


<h2>Total Earnings $<?=number_format($total_earnings);?></h2>


<!-- html5 graph -->
<script>
    window.onload = function ()
    {
        line2 = new RGraph.Line('myCanvas2', [<?php foreach ($stats['data'] as $stat) { echo $stat['Stat']['payout'].","; } ?>]);
        line2.Set('chart.hmargin', 5);
        line2.Set('chart.labels', [<?php foreach ($stats['data'] as $stat) { echo "'".date("m/d",strtotime($stat['Stat']['date']))."',"; } ?>]);
        line2.Set('chart.linewidth', 3);
        line2.Set('chart.shadow', true);
        line2.Set('chart.shadow.offsetx', 2);
        line2.Set('chart.shadow.offsety', 2);
        line2.Set('chart.ymax', 1000);
        line2.Set('chart.gutter', 50);
        line2.Set('chart.noxaxis', true);
        line2.Set('chart.noendxtick', true);
		line2.Set('chart.colors', ['#74d501']);
        //line2.Set('chart.title', 'Earnings Report');

        line2.Draw();

        line3 = new RGraph.Line('myCanvas2', [<?php foreach ($stats['data'] as $stat) { echo $stat['Stat']['conversions'].","; } ?>]);
        line3.Set('chart.hmargin', 5);
        line3.Set('chart.linewidth', 3);
        line3.Set('chart.shadow', true);
        line3.Set('chart.shadow.offsetx', 2);
        line3.Set('chart.shadow.offsety', 2);
        line3.Set('chart.yaxispos', 'right');
        line3.Set('chart.noendxtick', true);
        line3.Set('chart.background.grid', false);
        line3.Set('chart.ymax', 200);
        line3.Set('chart.colors', ['#0186d5', '#74d501']);
        line3.Set('chart.units.pre', '$');
        line3.Set('chart.gutter', 50);
        line3.Set('chart.key', ['Conversions','Earnings']);
        line3.Set('chart.key.background', 'rgba(123,255,255,0.5)');
	
        line3.Draw();
    }
</script>
<!-- end html5 graph -->