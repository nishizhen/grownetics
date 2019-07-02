<?php
/**
 * @var \App\Model\Entity\Notification[]|\Cake\Collection\CollectionInterface $notifications
 */
?>
<div class="nav notify-row" id="top_menu">
    <ul class="nav top-menu">
        <li id="header_inbox_bar" class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle">
                <i class="fa fa-bell-o"></i>
                <span class="badge bg-theme"><?= sizeof($notifications) ?></span>
            </a>
            <ul class="notificationsPopout dropdown-menu extended inbox">
                <div class="notify-arrow notify-arrow-green"></div>
                <li>
                    <p class="notificationPopoutHeader green">You have <?= sizeof($notifications) ?> recent notifications</p>
                </li>
                <?php foreach ($notifications as $notification): ?>
                    <li class="notificationPopoutEntry">
                        <a href="/notifications/view/<?=$notification->id?>">
                            <?php if ($notification->user) { ?>
                                <span class="photo"><img src="https://www.gravatar.com/avatar/<?= md5( strtolower( trim( $notification->user->email ) ) )?>?d=identicon" class="img-circle" width="20"> </span>
                                <span class="subject">
                                    <span class="from"><?= $notification->user->name ?></span>
                                    <abbr class="time timeago" title="<?= $notification->created->i18nFormat(null, env('TIMEZONE')) ?>"></abbr>
                                </span>
                            <?php } ?>
                            <span class="message">
                                <?php echo $notification->message; ?>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li>
                    <a href="/notifications">See All Notifications</a>
                </li>
            </ul>
        </li>
    </ul>
</div>