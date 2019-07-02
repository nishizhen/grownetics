<?php
/**
 * @var \App\View\AppView $this
 */
?>
<!--<iframe src="http://localhost:4000/?name=--><?//=$this->request->session()->read('Auth.User.name')?><!--" style="height:400px;width:100%;"></iframe>-->
<h3>ANNOUNCEMENTS</h3>
<div class="chat_wrapper">
	<div id="chatBox">
	<?php foreach($chats as $chat) {
	    ?>
		<div class="desc">
			<div class="thumb">
                <?php if ($chat['user']) { ?>
				    <span class="badge bg-theme"><img src="https://www.gravatar.com/avatar/<?php echo md5( strtolower( trim( $chat['user']->email ) ) );
					?>?d=identicon" class="img-circle" width="20"></span>
                <?php } ?>
			</div>
			<div class="details">
				<div class="message"><b><?=$chat['user']['name']?></b> <?=$chat['message']?></div>
								<p><muted><abbr class="timeago" title="<?php
					$newDate = $chat['created'];
					$formatted = date_format($newDate, 'Y-m-d H:i:sP');
					echo $formatted;
					?>">
				</abbr></muted><br></p>
			<p></p>
			</div>
		</div>
	<?php } ?>
	</div>
	<div class="panel">
		<input type="text" name="message" id="message" placeholder="Message" maxlength="500" />
		<button id="send-btn">Send</button>
	</div>
</div>