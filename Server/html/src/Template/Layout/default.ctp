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
    if (env('CERES')) {
        echo $this->Html->meta(
            'ceres.ico',
            '/ceres.ico',
            ['type' => 'icon']
        );
    } else {
        echo $this->Html->meta('icon');
    }
    echo $this->AssetCompress->css('all');
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    if (env('DEV')) {
        echo $this->AssetCompress->script('headlibs.dev');
    } else {
        echo $this->AssetCompress->script('headlibs');
    }
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Ruda" rel="stylesheet">
    <?php # Hide the Zendesk Widget if in Dev mode, or Ceres mode.
    if (!env('DEV') && !env('CERES')) { ?>
        <!-- Start of grownetics Zendesk Widget script -->
        <script>
            /*<![CDATA[*/
            window.zEmbed || function(e, t) {
                var n, o, d, i, s, a = [],
                    r = document.createElement("iframe");
                window.zEmbed = function() {
                    a.push(arguments)
                }, window.zE = window.zE || window.zEmbed, r.src = "javascript:false", r.title = "", r.role = "presentation", (r.frameElement || r).style.cssText = "display: none", d = document.getElementsByTagName("script"), d = d[d.length - 1], d.parentNode.insertBefore(r, d), i = r.contentWindow, s = i.document;
                try {
                    o = s
                } catch (e) {
                    n = document.domain, r.src = 'javascript:var d=document.open();d.domain="' + n + '";void(0);', o = s
                }
                o.open()._l = function() {
                    var e = this.createElement("script");
                    n && (this.domain = n), e.id = "js-iframe-async", e.src = "https://assets.zendesk.com/embeddable_framework/main.js", this.t = +new Date, this.zendeskHost = "grownetics.zendesk.com", this.zEQueue = a, this.body.appendChild(e)
                }, o.write('<body onload="document._l();">'), o.close()
            }();
            /*]]>*/
        </script>
        <!-- End of grownetics Zendesk Widget script -->
        <?php if (env('HOTJAR_SITE_ID')) { ?>
            <!-- Hotjar Tracking Code for grownetics.co -->
            <script>
                (function(h, o, t, j, a, r) {
                    h.hj = h.hj || function() {
                        (h.hj.q = h.hj.q || []).push(arguments)
                    };
                    h._hjSettings = {
                        hjid: <?= env('HOTJAR_SITE_ID') ?>,
                        hjsv: 6
                    };
                    a = o.getElementsByTagName('head')[0];
                    r = o.createElement('script');
                    r.async = 1;
                    r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
                    a.appendChild(r);
                })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
            </script>
        <?php } ?>
    <?php } ?>
    <script src="<?= env('REMOTE_URL') ?>:8989/socket.io/socket.io.js"></script>
</head>
<body<?php if (isset($bodyClass)) {
            echo ' class="' . $bodyClass . '"';
        } ?>>
    <section id="container" <?php if (isset($hideSidebar)) { ?> class='sidebar-closed' <?php } ?>>
        <!-- **********************************************************************************************************************************************************
            TOP BAR CONTENT & NOTIFICATIONS
            *********************************************************************************************************************************************************** -->
        <!--header start-->
        <?php if (!isset($hideHeader)) { ?>
            <header class="header black-bg<?php if (env('DEV')) {
                                                echo " header-dev";
                                            } ?>">
                <?php if (!isset($hideSidebar)) { ?>
                    <div class="sidebar-toggle-box">
                        <div class="fa fa-bars tooltips" data-placement="right"></div>
                    </div>
                <?php } ?>
                <!--logo start-->
                <a href="/" class="logo">
                    <b>Grownetics</b> - <?= env('FACILITY_NAME') ?>
                </a>
                <!--logo end-->

                <?php //if ($this->params['controller']=='dash') {
                //echo $this->element('sound_settings');
                //}
                ?>

                <?php echo $this->cell('Notifications::popout'); ?>

                <div class="top-menu">
                    <ul class="nav pull-right top-menu hidden-xs">
                        <li><a data-toggle="dropdown" class="dropdown-toggle">
                                <i class="fa fa-users"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li<?php if (!$organizationId) { echo " class='active'"; }?>><?= $this->Html->link("No Organization", ['controller' => 'Organizations', 'action' => 'setActiveOrganization']) ?></li>
                                <?php foreach ($userOrganizations as $organization) { ?>
                                    <li<?php if ($organizationId == $organization->id) { echo " class='active'"; }?>><?= $this->Html->link($organization->label, ['controller' => 'Organizations', 'action' => 'setActiveOrganization', $organization->id]) ?></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <li><a data-toggle="dropdown" class="dropdown-toggle">
                                <i class="fa fa-life-saver"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <?php if (env('DEV')) { ?>
                                    <li><a href='http://localhost:8000' target='_new'><i class="fa fa-ticket"></i> Documentation</a></li>
                                <?php } else { ?>
                                    <li><a href='/docs/' target='_new'><i class="fa fa-ticket"></i> Documentation</a></li>
                                <?php } ?>
                                <li><a href="tel:1-844-476-9638"><i class="fa fa-phone"></i> 1 (844) 476-9638</a></li>
                                <li><a href="mailto:support@grownetics.co" target="_blank"><i class="fa fa-envelope"></i> support@grownetics.co</a></li>
                            </ul>
                        </li>
                        <li>
                            <?php if ($this->request->session()->read('Auth.User.id')) { ?>
                                <a class='logout' href='/users/logout'>Logout</a>
                            <?php } else { ?>
                                <a class='login' href='/users/login'>Login</a>
                            <?php } ?>
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
        <?php if (!isset($hideSidebar)) { ?>
            <aside>
                <?php
                if (CERES) {
                    echo $this->element('ceres_nav');
                } else {
                    echo $this->element('nav');
                }
                ?>
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
                    <?php if (env('DEMO')) { ?>
                        <div class="alert alert-info" onclick="this.classList.add('hidden');"><b>This is a demo instance!</b> No data here is real, and any data entered may be wiped out.</div>
                    <?php } ?>
                </div>
                <?php echo $this->fetch('content'); ?>
            </section>
        </section>
        <?php if (!isset($hideFooter)) { ?>
            <footer class="site-footer">
                <div class="text-center">
                    <?= env('FACILITY_NAME') ?> - Grownetics <a href="/pages/changelog" title="Build ID: <?= $BUILD_ID ?> Build Date: <?= $BUILD_DATE ?>"><?= env('VERSION') ?></a> - <?= date('Y') ?>
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
                _paq.push(['setTrackerUrl', 'https://api-ceecaaea119f54c83c04dc84cba63753.oasis.sandstorm.io']);
                _paq.push(['setSiteId', <?= env('FACILITY_ID') ?>]);
                _paq.push(['setApiToken', 'ZqcVb83PUpTqyVoBLeLFpiJRukdOoI6qJTXINRcVuLo']);
                var d = document,
                    g = d.createElement('script'),
                    s = d.getElementsByTagName('script')[0];
                g.type = 'text/javascript';
                g.async = true;
                g.defer = true;
                g.src = 'https://8wr8xyxtmiu3kb9kjgfy.oasis.sandstorm.io/embed.js';
                s.parentNode.insertBefore(g, s);
            })();
        </script> <?php } ?>
    </body>

</html>