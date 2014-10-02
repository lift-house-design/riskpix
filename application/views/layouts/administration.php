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
	<meta property="og:site_name" content="Slang.org: Internet Slang"/>
	<meta property="fb:admins" content=""/>
	<meta name="twitter:card" content="summary"/>
	<meta name="twitter:url" content="<?php echo $meta['url'] ?>"/>
	<meta name="twitter:title" content="<?php echo $meta['title'] ?>"/>
	<meta name="twitter:description" content="<?php echo $meta['description'] ?>"/>
	<meta name="twitter:image" content="<?php echo $meta['image'] ?>"/>
	<meta itemprop="name" content="<?php echo $meta['title'] ?>"/>
	<meta itemprop="description" content="<?php echo $meta['description'] ?>"/>
	<meta itemprop="image" content="<?php echo $meta['image'] ?>"/>

	<!-- assets -->
    <?php echo min_css($min_css) ?>
    <?php echo css($css) ?>
    <?php echo less_css($less_css) ?>
    <?php echo min_js($min_js) ?>
	<?php echo js($js) ?>
</head>
<body>
	<?php echo $yield_topbar ?>
	<?php echo $yield_notifications ?>
	<?php echo $yield ?>
	<?php echo $yield_analytics ?>
	<?php /* Should add seo aside from slang.org */ ?>
</body>
</html>