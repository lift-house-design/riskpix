<? /* use $refresh to reload a particular image */ ?>
<h1>Your Photos</h1>
Tap a thumbnail to retake a photo.
<br/>
<div>
	<? foreach($photos as $p){ ?>
		<? $page = $p['photo_num']; ?>
		<? $url = $p['url']; ?>
		<div class="w33pc pad2 align-center">
			<a href="/photo/<?= $page ?>/1">
				<img class="full" src="<?= $p['url_thumb'] ?><?= $page == $refresh ? '?'.time() : '' ?>"/>
			</a>
		</div>
		<? if($page == 3){ ?>
			</div><div>
		<? } ?>
	<? } ?>
</div>
<br/>
<form method="post">
	<input type="submit" name="next" value="NEXT: Submit your photos"/>
</form>

