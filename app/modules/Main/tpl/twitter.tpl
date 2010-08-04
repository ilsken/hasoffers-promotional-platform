
<h1>Tweet on Twitter</h1>

<div id="pagelogo"><img src="/images/logo-twitter.gif" border="0" alt="Twitter" /></div>

<ol>
	<li>Choose the the text to Tweet</li>
	<li>Click the Tweet button next to the text box</li>
	<li>Specify your Twitter username and password in the popup</li>
	<li>Tweet it to all your followers</li>
	<li>Earn money having your followers signup to <?=Conf::read('ENV.CompanyName');?></li>
</ol>

<?php if(!empty($creatives)) : ?>
	<?php foreach ($creatives as $creative) : ?>
		<form method="POST" action="/twitterpost" id="tweet<?=$creative['OfferFile']['id'];?>">
			<p>
				<textarea name="message" class="textboxbig" maxlength="140"><?=$creative['OfferFile']['code']." ".$tracking['click_url'];?></textarea> 
				<a href="#" onclick="$('#tweet<?=$creative['OfferFile']['id'];?>').submit();" name="twitter_share"><img src="/images/button-tweet.gif" alt="Tweet This"/></a>
			</p>
		</form>
	<?php endforeach; ?>
<?php else: ?>
	<form method="POST" action="/twitterpost"id="tweet0">
		<p>
			<textarea name="message" class="textboxbig"><?=$tracking['click_url'];?></textarea> 
			<a href="#" onclick="$('#tweet0').submit();" name="twitter_share"><img src="/images/button-tweet.gif" alt="Tweet This"/></a>
		</p>
	</form>
<?php endif; ?>




