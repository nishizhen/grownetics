<?php
/**
 * @var \App\View\AppView $this
 */
?>

<!doctype html>
<html>
<head>
	<?php echo $this->Html->charset();
	$this->Html->script('header/notificationsPopout', ['block' => 'scriptBottom']); ?>
	<title>
		Grownetics - <?= $this->fetch('title') ?>
	</title>
	<?php
        if (CERES) {
            echo $this->Html->meta(
	'ceres.ico',
	'/ceres.ico',
	['type' => 'icon']
	);
	} else {
	echo $this->Html->meta('icon');
	}
	$this->loadHelper('AssetCompress.AssetCompress');
	echo $this->AssetCompress->css('all');
	echo $this->fetch('meta');
	echo $this->fetch('css');
	echo $this->fetch('script');
	?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Ruda" rel="stylesheet">
</head>
<body<?php if (isset($bodyClass)){echo ' class="'.$bodyClass.'"';}?>>
<section id="container"<?php if (isset($hideSidebar)) { ?> class='sidebar-closed'<?php } ?>>
<!-- **********************************************************************************************************************************************************
TOP BAR CONTENT & NOTIFICATIONS
*********************************************************************************************************************************************************** -->
<!--header start-->
<?php if (!isset($hideHeader)) { ?>
<header class="header black-bg<?php if (env('DEV')) { echo " header-dev"; } ?>">
<?php if (!isset($hideSidebar)) { ?>
<div class="sidebar-toggle-box">
	<div class="fa fa-bars tooltips" data-placement="right"></div>
</div>
<?php } ?>
<!--logo start-->
<a href="/" class="logo">
	<b>Grownetics</b> - <?=env('FACILITY_NAME')?>
</a>
<!--logo end-->

<div class="top-menu">
	<ul class="nav pull-right top-menu hidden-xs">
		<li><a data-toggle="dropdown" class="dropdown-toggle">
			<i class="fa fa-life-saver"></i>
		</a>
			<ul class="dropdown-menu pull-right">
				<li><a href='https://support.grownetics.co/hc/en-us'><i class="fa fa-ticket"></i> Zendesk Page</a></li>
				<li><a href="tel:1-844-476-9638"><i class="fa fa-phone"></i> 1 (844) 476-9638</a></li>
				<li><a href="sms:1-720-420-6011"><i class="fa fa-commenting"></i> 1 (720) 420-6011</a></li>
				<li><a href="mailto:support@grownetics.co"><i class="fa fa-envelope"></i> support@grownetics.co</a></li>
			</ul>
		</li>
		<li>
			<?php if ($this->request->session()->read('Auth.User.id')) { ?>
			<a class='logout' href='/users/logout'>Logout</a>
			<?php } else {?>
			<a class='login' href='/users/login'>Login</a>
			<?php }?>
		</li>
	</ul>
</div>

</header>
<?php } ?>
<!--header end-->

<!-- **********************************************************************************************************************************************************
MAIN SIDEBAR MENU
*********************************************************************************************************************************************************** -->
<!--sidebar start-->
<?php if (0 && !isset($hideSidebar)) { ?>
<aside>
</aside>
<?php } ?>
<!--sidebar end-->

<!-- **********************************************************************************************************************************************************
MAIN CONTENT
*********************************************************************************************************************************************************** -->
<!--main content start-->
<section id="main-content">
	<section class="wrapper site-min-height">
		<div id='flashMessages'>
			<?= $this->Flash->render() ?>
			<?= $this->Flash->render('auth', [
			'element' => 'auth_custom'
			]); ?>
		</div>
		<?php echo $this->fetch('content'); ?>
	</section>
</section>
<?php if (!isset($hideFooter)) { ?>
<footer class="site-footer">
	<div class="text-center">
		<?=env('FACILITY_NAME')?> - Grownetics <?=env('VERSION')?> - <?=date('Y')?>
		<a href="#" class="go-top">
			<i class="fa fa-angle-up"></i>
		</a>
	</div>
</footer>
<?php } ?>
<!--footer end-->
</section>
<?php
        echo $this->AssetCompress->script('libs');
echo $this->fetch('scriptBottom');
?>
<script>
    jQuery(document).ready(function() {
        jQuery("abbr.timeago").timeago();
    });
</script>
<?php if (env('FACILITY_ID')) { ?>
<!-- Piwik -->
<script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
        _paq.push(['setTrackerUrl',  'https://api-ceecaaea119f54c83c04dc84cba63753.oasis.sandstorm.io']);
        _paq.push(['setSiteId', <?=env('FACILITY_ID')?>]);
        _paq.push(['setApiToken', 'ZqcVb83PUpTqyVoBLeLFpiJRukdOoI6qJTXINRcVuLo']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.type='text/javascript'; g.async=true; g.defer=true; g.src='https://8wr8xyxtmiu3kb9kjgfy.oasis.sandstorm.io/embed.js'; s.parentNode.insertBefore(g,s);
    })();
</script> <?php } ?>
</body>
</html>