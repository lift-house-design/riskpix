<?php /* use $refresh to reload a particular image */ ?>
<h1>Your Photos</h1>
Tap a thumbnail to retake a photo.
<br/>
<div class="align-center">
	<?php foreach($photos as $p){ ?>
		<?php $page = $p['photo_num']; ?>
		<?php $url = $p['url']; ?>
		<div class="w33pc pad2 align-center">
			<a href="/photo/<?php echo $page ?>/1">
				<img class="full" src="<?php echo $p['url_thumb'] ?>?<?php echo time() ?>"/>
			</a>
		</div>
		<?php if($page % 3 == 0){ ?>
			</div><div>
		<?php } ?>
	<?php } ?>
</div>
<br/>
<form method="post">
	<input type="submit" name="next" value="NEXT: Submit your photos"/>
</form>

