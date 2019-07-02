<p>Hello <?=$user['name']?>,</p>

<p>You have been invited to create a Grownetics account. Set your password using the following link: <a href='http://<?=env('HTTP_HOST')?>/users/password/<?=$token?>'>http://<?=env('HTTP_HOST')?>/users/password/<?=$token?></a></p>

<p>If you have any questions don't hesitate to email us at support@grownetics.co or give us a call at +1 844-GROWNET</p>