<?php
/**
 * @var \App\View\AppView $this
 */
?>



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
                <?=env('FACILITY_NAME')?> - Grownetics <span title="Build ID: <?=env('BUILD_ID')?> Build Date: <?=env('BUILD_DATE')?>"><?=env('VERSION')?></span> - <?=date('Y')?>
                <a href="#" class="go-top">
                    <i class="fa fa-angle-up"></i>
                </a>
            </div>
        </footer>
    <?php } ?>
    <!--footer end-->


