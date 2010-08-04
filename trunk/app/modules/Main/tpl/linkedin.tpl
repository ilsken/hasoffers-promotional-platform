

<h1>Post on LinkedIn</h1>

<div id="pagelogo"><img src="/images/logo-linkedin.gif" border="0" alt="linkedin" /></div>

<ol>
	<li>Choose the the text to post on your LinkedIn profile</li>
	<li>Click the Post button next to the text box</li>
	<li>Login to LinkedIn if you are not</li>
	<li>Confirm the title and summary of the post</li>
	<li>Include your comment to Post with the update</li>
	<li>Click Share at the bottom</li>
	<li>Earn money having your connections on LinkedIn signup to <?=Conf::read('ENV.CompanyName');?></li>
</ol>

<?php if(!empty($creatives)) : ?>
	<?php foreach ($creatives as $creative) : ?>
		<p>
			<textarea name="creativeCode" class="textboxbig" file_id="<?=$creative['OfferFile']['id'];?>"><?=$creative['OfferFile']['code'];?></textarea> 
			<a href="#" name="linkedin_share" id="linkedinShare<?=$creative['OfferFile']['id'];?>" target="_blank"><img src="/images/linkedin-btn.png" alt="Post This"/></a>
		</p>
	<?php endforeach; ?>
<?php else: ?>
	<p>
		<textarea name="creativeCode" class="textboxbig" file_id="0"></textarea> 
		<a href="#" name="linkedin_share" id="linkedinShare0" target="_blank"><img src="/images/linkedin-btn.png" alt="Post This"/></a>
	</p>
<?php endif; ?>


<script type="text/javascript">
//<![CDATA[

$("textarea[name=creativeCode]").change( function() {
		
	var file_id = $(this).attr('file_id');
	var code = $(this).val();
	
	var link = $("a#linkedinShare"+file_id);	

	if ( file_id == 0 ) {
		var url = "http://www.linkedin.com/shareArticle?mini=true&url=<?=urlencode($tracking['click_url']);?>&title="+escape(code)+"&summary="+escape(code)+"&source=<?=urlencode(Conf::read('ENV.CompanyName'));?>";

		link.attr('href', url );
				
		return true;	
	} else {
		$.getJSON('ajax_text_ad?id='+file_id+'&source=<?=$source;?>&code='+escape(code), function(data) {
			if ( data.url != '' ) {
				var url = "http://www.linkedin.com/shareArticle?mini=true&url="+escape(data.url)+"&title="+escape(data.code)+"&summary="+escape(data.code)+"&source=<?=urlencode(Conf::read('ENV.CompanyName'));?>";

				link.attr('href', url );
				
				return true;
			}
		});
	}

	return false;
}).change();
//]]>
</script>
 