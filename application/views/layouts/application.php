<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<!-- Nerd Stuff -->
    <meta charset="utf-8">
    <meta name="Content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- SEO Stuff -->
    <title><?php echo $meta['title'] ?></title>
    <meta name="description" content="<?php echo $meta['description'] ?>">
    <meta name="keywords" content="<?php echo $meta['keywords'] ?>"/>
	<meta name="copyright" content="<?php echo $copyright ?>"/>

	<!-- Social SEO Stuff -->
	<meta property="og:title" content="<?php echo $meta['title'] ?>"/>
	<meta property="og:description" content="<?php echo $meta['description'] ?>"/>
	<meta property="og:type" content="Article"/>
	<meta property="og:url" content="<?php echo $meta['url'] ?>"/>
	<meta property="og:image" content="<?php echo $meta['image'] ?>"/>
	<meta property="og:site_name" content="<?php echo $meta['title'] ?>"/>
	<meta property="fb:admins" content=""/>
	<meta name="twitter:card" content="summary"/>
	<meta name="twitter:url" content="<?php echo $meta['url'] ?>"/>
	<meta name="twitter:title" content="<?php echo $meta['title'] ?>"/>
	<meta name="twitter:description" content="<?php echo $meta['description'] ?>"/>
	<meta name="twitter:image" content="<?php echo $meta['image'] ?>"/>
	<meta itemprop="name" content="<?php echo $meta['title'] ?>"/>
	<meta itemprop="description" content="<?php echo $meta['description'] ?>"/>
	<meta itemprop="image" content="<?php echo $meta['image'] ?>"/>

	<?php /*
	<!-- Favicons -->
	<link rel="shortcut icon" href="/assets/favicons/favicon.ico">
  <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
  <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196">
  <link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160">
  <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
  <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
  <meta name="msapplication-TileColor" content="#ff0000">
  <meta name="msapplication-TileImage" content="/mstile-144x144.png">
	*/?>

	<!-- assets -->
  <?php echo css($css) ?>
	<?php echo js($js) ?>

	<!-- Thanks, Bill -->
	<!--[if lt IE 9]>
		<script src="/assets/js/IE9.js"></script>
	<![endif]-->
</head>
<body>
  	<?php echo $yield_topbar ?>
  	<?php echo empty($yield_progress) ? '' : $yield_progress ?>
  	<?php echo empty($yield_banner) ? '' : $yield_banner ?>
  	<?php echo empty($yield_home_text) ? '' : $yield_home_text ?>
  	<div class="center-wrap">
  		<?php echo $yield_notifications ?>
  		<?php echo $yield ?>
  	</div>
  	<?php echo $yield_analytics ?>
  	<?php echo empty($yield_claim_number) ? '' : $yield_claim_number ?>
  	<?php echo empty($yield_seo) ? '' : $yield_seo ?>
  	<?php echo $yield_footer ?>



  	<div class="overlay-bg"></div>
  	<div class="overlay-circle">
  		<table><td>checking VIN...</td></table>
  	</div>
  	<img class="overlay-loading" src="/assets/img/loading.gif"/>
  	<?php /* Should add seo aside from slang.org */ ?>

    <?php echo $yield_bottombar ?>
</body>
</html>
