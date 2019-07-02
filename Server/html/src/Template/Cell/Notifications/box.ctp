<h3>NOTIFICATIONS</h3>
<div id='notificationTemplate' style='display: none;'>
	<div class="desc">
		<div class="thumb">
			<span class="badge bg-theme"><i class="fa"></i></span>
		</div>
		<div class="details">
				<div class='message'></div><br />
				<p><muted><abbr class="timeago new" title=""></abbr></muted>
			</p>
		</div>
	</div>
</div>
<div id='notifications'  style="height: 105%;">
	<div id="notificationsBox">
		<?php foreach($notifications as $notification) { ?>
		<div class="desc">
			<div class="thumb">
				<span class="badge bg-theme"><i class="fa fa-comment"></i></span>
			</div>
			<div class="details">
				<div class="message"><?=$notification['message']?></div>
				<p><muted><abbr class="timeago" title="<?= $notification->created->i18nFormat(null, env('TIMEZONE')); ?>"></abbr></muted>
				</p>
				<p></p>
			</div>
		</div>
		<?php } ?>
	</div>
</div>