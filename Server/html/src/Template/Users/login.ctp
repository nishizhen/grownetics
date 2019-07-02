<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="login-page">
      <div class="container">
              <?php

            echo $this->Form->create('User',['templateVars'=>['header'=>'Sign in Now','class'=>'form-login']]); ?>
                <?= $this->Flash->render('auth', [
                    'element' => 'auth_custom'
                ]); ?>
            <div class="login-wrap">
                <?=$this->Form->input('email',array('class'=>'form-control','autofocus'=>true));?>
                <br>
                <?=$this->Form->input('password',array('class'=>'form-control'));?>
                <br>
                <div class="form-group" style="border: 0px !important; padding: 0px !important; -webkit-box-shadow: none !important;">
                    <label for="stayLoggedIn" title="Don't check this if you're on a public computer" class="col-lg-2">Stay logged in</label>
                    <?php $myTemplates = [
                    'input' => '<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>',
                    ];
                    $this->Form->setTemplates($myTemplates);
                    echo $this->Form->checkbox('userStayLoggedIn', ['id'=>'stayLoggedIn','style'=>'width: 2em; margin-left: 1em','class'=>'form-control col-lg-10','title' => 'Don\'t check this if you\'re on a public computer']);?>
                </div>
                <button class="btn btn-theme btn-block" href="#" type="submit" id='submit'><i class="fa fa-lock"></i> SIGN IN</button>
                <hr>
                <a href="/users/reset">Reset Password</a>
                <?=$this->Form->end()?>
                <?php if (env('DEV')) { ?>
                    <div class="col-md-4 col-sm-4 mb">
                    <div class="darkblue-panel pn">
                        <div class="darkblue-header">
                            <h5>Login as role</h5>
                        </div>
                        <footer>
                            Select a role:
                            <a href="/users/login/1?<?=$_SERVER['QUERY_STRING']?>">Admin</a>
                            <a href="/users/login/2?<?=$_SERVER['QUERY_STRING']?>">Owner</a>
                            <a href="/users/login/3?<?=$_SERVER['QUERY_STRING']?>">Grower</a>
                        </footer>
                        <p>&nbsp;</p>
                    </div><!-- -- /darkblue panel ---->
                </div><!-- /col-md-4 -->
                <?php } ?>

            </div>
      </div>
</div>
