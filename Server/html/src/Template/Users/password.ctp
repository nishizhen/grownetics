<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="login-page">
    <div class="container">
        <?php echo $this->Form->create('User',['templateVars'=>['header'=>'Reset Password','class'=>'form-login']]); ?>
        <?= $this->Flash->render('auth', [
            'element' => 'auth_custom'
        ]); ?>
        <div class="login-wrap">
            <label class="col-sm-2 col-sm-2 control-label">Password</label>
            <?=$this->Form->password('password',array('class'=>'form-control','autofocus'=>true));?>
            <br/>
            <label class="col-sm-2 col-sm-2 control-label">Password Confirm</label>
            <?=$this->Form->password('password_confirm',array('class'=>'form-control','autofocus'=>true));?>
            <button class="btn btn-theme btn-block" href="#" type="submit" id='submit'><i class="fa fa-lock"></i> RESET PASSWORD</button>
            <hr>
            <?=$this->Form->end()?>
        </div>
    </div>
</div>

