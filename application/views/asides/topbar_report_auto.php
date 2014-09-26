<?
switch($progress)
{
	case 'take-photos': 
		$nav = array('/report/2', 'VIN', 'PHOTOS');
		$prog = array('vehicle info',"<b>take photos</b>",'submit');
		break;
	case 'submit': 
		$nav = array('/photo/1', 'Photos', 'SUCCESS!');
		$prog = array('vehicle info','take photos','<b>submit</b>');
		break;
	default: 
		$nav = array('/report/1', 'Welcome', 'VIN');
		$prog = array('<b>vehicle info</b>','take photos','submit');
		break;
}
?>

<div id="topbar-report">
	<div class="center-wrap pad10">
		<div class="w33pc">
			<a href="<?= $nav[0] ?>" alt="<?= $site_name ?>"><?= $nav[1] ?></a>
		</div>
		<div class="w33pc">
			<b><?= $nav[2] ?></b>
		</div>
		<div class="w33pc">
			call for help
		</div>
	</div>
</div>

<div class="progress pad8 pad4t pad6b">
	<div>
		<div class="w33pc"><?= $prog[0] ?></div>
		<div class="w33pc"><?= $prog[1] ?></div>
		<div class="w33pc"><?= $prog[2] ?></div>
	</div>
	<div class="bar">
		<div class="<?= $progress ?>"></div>
	</div>
</div>
<div class="spacer10"></div>