
<form method="POST">

<h1>Share on Facebook</h1>

<div id="pagelogo"><img src="/images/logo-facebook.gif" border="0" alt="linkedin" /></div>

<ol>
	<li>Choose whether to say <a href="#ilike">I Like</a> <?=Conf::read('ENV.CompanyName');?> or to <a href="#share">Share</a> on your Facebook</li>
	<li>To tell your friends you like <?=Conf::read('ENV.CompanyName');?>, just click the I Like button</li>
	<li>To share <?=Conf::read('ENV.CompanyName');?> with your friends, select copy under Share <?=Conf::read('ENV.CompanyName');?></li>
	<li>Login to Facebook if you are not</li>
	<li>Click Share at the bottom right of the window</li>
	<li>Earn money having your Friends on Facebook signup to <?=Conf::read('ENV.CompanyName');?></li>
</ol> 

<h2>I like <?=Conf::read('ENV.CompanyName');?><a name="ilike"></a></h2>
<p>You can easily tell your friends on Facebook that you like <?=Conf::read('ENV.CompanyName');?>. Simply click the I Like button below.</p>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?=urlencode("http://".$_SERVER['HTTP_HOST']."/link?url=".$tracking['click_url']);?>+&amp;layout=standard&amp;show_faces=true&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=25" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:25px;" allowTransparency="true"></iframe>

<h2>Share <?=Conf::read('ENV.CompanyName');?><a name="share"></a></h2>
<p>Share <?=Conf::read('ENV.CompanyName');?> with your friends by providing including an update on why you like <?=Conf::read('ENV.CompanyName');?>. </p>


<?php if(!empty($creatives)) : ?>
	<?php foreach ($creatives as $creative) : ?>
		<p>
			<textarea name="text" class="textboxbig" id="creativeCode<?=$creative['OfferFile']['id'];?>"><?=$creative['OfferFile']['code'];?></textarea> 
			<a name="fb_share" file_id="<?=$creative['OfferFile']['id'];?>" type="button_count" href="http://www.facebook.com/sharer.php">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
		</p>
	<?php endforeach; ?>
<?php else: ?>
	<p>
		<textarea name="text" class="textboxbig" id="creativeCode0"></textarea> 
		<a name="fb_share" file_id="0" type="button_count" href="http://www.facebook.com/sharer.php">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
	</p>
<?php endif; ?>

</form>


<script type="text/javascript">
//<![CDATA[

$("a[name=fb_share]").click( function() {
		
	var file_id = $(this).attr('file_id');
	var link = $(this);
	
	var code = $("textarea#creativeCode"+file_id).val();
	
	if ( file_id == 0 ) {
		var share_url = "http://<?=$_SERVER['HTTP_HOST'];?>/link?url=<?=urlencode($tracking['click_url']);?>&txt="+escape(code);

		var url = "http://www.facebook.com/sharer.php?u="+escape(share_url);
		link.attr('href', url );
					
		return true;
	} else {
		$.getJSON('ajax_text_ad?id='+file_id+'&source=<?=$source;?>&code='+escape(code), function(data) {
			if ( data.url != '' ) {
				var share_url = "http://<?=$_SERVER['HTTP_HOST'];?>/link?url="+escape(data.url)+"&txt="+escape(data.code);

				var url = "http://www.facebook.com/sharer.php?u="+escape(share_url);
				link.attr('href', url );
					
				return true;
			}
		});
	}

	return false;
});
//]]>
</script>
 