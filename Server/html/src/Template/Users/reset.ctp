<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="login-page">
    <div class="container">
        <?php echo $this->Form->create('User',['url'=>'/users/password','templateVars'=>['header'=>'Request Password Reset','class'=>'form-login']]); ?>
        <?= $this->Flash->render('auth', [
            'element' => 'auth_custom'
        ]); ?>
        <div class="login-wrap">
            <?=$this->Form->input('email',array('class'=>'form-control','autofocus'=>true));?>
            <button class="btn btn-theme btn-block" href="#" type="submit" id='submit'><i class="fa fa-lock"></i> RESET PASSWORD</button>
            <hr>
            <?=$this->Form->end()?>
        </div>
    </div>
</div>
