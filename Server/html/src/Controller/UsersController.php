<?php


namespace App\Controller;

use App\Lib\SystemEventRecorder;
use Cake\Mailer\Email;

/**
 * @property \App\Model\Table\UserContactMethodsTable $UserContactMethods
 * @property \App\Model\Table\RolesTable $Roles
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController {

    public function isAuthorized($user)
    {
        // All users can edit their own account, and logout.
        if (in_array($this->request->action, ['login'])) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function age_verification() {
        if ($this->request->is('post')) {
            $birthYear = $this->request->data['birth_year'];
            if (date('Y') - $birthYear >= 21) {
                $this->Cookie->write('is21',true, false, '1 hour');
                $this->redirect('/'); return;
            } else {
                $this->Flash->error(__('You must be at least 21 to access our site.'),'default',array('class'=>'error'));
            }
        }

        $this->layout = 'blank';
    }

    public function impersonate($userId = null) {
        $user = $this->Users->get($userId);
        if (!$user) {
            $this->Flash->error('Invalid access token');
            return $this->redicect('/users/');
        }
        $this->Auth->setUser($user->toArray());
        $this->Flash->success('Successfully impersonating '.$user->name);
        return $this->redirect('/');
    }

    public function login($roleId=null) {
        # If we're in development and have a roleID set, login as the first active user with that role.
        if (env('DEV') && $roleId) {
            $this->loadModel('UsersRoles');
            $userRole = $this->UsersRoles->find('all',[
                'conditions' => [
                    'role_id' => $roleId
                    // 'deleted' => false
                ]
            ])->first();
            $user = $this->Users->get($userRole->user_id);

            $this->Auth->setUser($user);
            return $this->redirect($this->Auth->redirectUrl());
        }
        if ($this->Auth->user('id')>0) {
            return $this->redirect($this->Auth->redirectUrl());
        }
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();

            if ($user) {               
                $this->Auth->setUser($user);
                if($this->request->data('userStayLoggedIn') == 1) {
                    $this->Cookie->configKey('CookieAuth', [
                        'expires' => '+30 days',
                        'httpOnly' => true
                    ]);
                    $siteSalt = 'cLJvJ2K}c#4mY7zp7s.mh';
                    $userEntity = $this->Users->get($user['id']);
                    $token = substr(hash('ripemd160',$siteSalt . time() . uniqid() . $this->data['password']),0,10);
                    $userEntity->access_code =  $token;
                    $this->Users->save($userEntity);
                    $this->Cookie->write('CookieAuth', [
                        'username' => $this->request->data('email'),
                        'token' => $token
                    ]);
                }
                $recorder = new SystemEventRecorder();
                $recorder->recordEvent('user_actions','user_logged_in',1,['type' => 'Users','email' => $this->request->getData('email')]);
                if ($this->FeatureFlags->getFlagValue("home_screen")) {
                    if ($this->Auth->redirectUrl() && $this->Auth->redirectUrl() != "/") {
                        return $this->redirect($this->Auth->redirectUrl());
                    } else {
                        return $this->redirect('/pages/home');
                    }
                } else {
                    return $this->redirect($this->Auth->redirectUrl());
                }
            }
            $this->Flash->error('Your username or password is incorrect.');
            $recorder = new SystemEventRecorder();
            $recorder->recordEvent('user_actions','user_failed_log_in_attempt',1,['type' => 'Users','email' => $this->request->getData('email')]);

            // $query = $this->Users->find('all',array('conditions'=>array('email'=>$this->request->data['User']['email'])));
            // $user = $query->first();
            // // echo "<pre>"; print_r($this->User->find('all'));
            // // print_r($this->request->data['User']['email']); die();
            // if (isset($user['User']) && $user['User']['role']=='invitee') {
            //     if ($user['User']['access_code']==$this->request->data['User']['password']) {
            //         $this->Flash->error(__('Welcome to Grownetics! Just verify your information below, set a password, and we\'ll get started!'),'default',array('class'=>'success'));
            //         return $this->redirect('/users/register/'.$user['User']['access_code']);
            //     } else {
            //         dbgd("Unknown error. Please report. UsersCont:39");
            //     }
            // } else {
            //     if ($this->Auth->login()) {
            //         return $this->redirect($this->Auth->redirect());
            //     }
            // }
            // $this->Flash->error(__('Invalid username or password, try again'),'default',array(),'auth');
        } else {
            // dbg($this->request);
            // dbgd("?");
        }
        $this->set('hideSidebar',true);
        $this->set('hideHeader',true);
        $this->set('hideFooter',true);
        $this->set('bodyClass','login');
    }

    public function logout() {
        $recorder = new SystemEventRecorder();
        $recorder->recordEvent('user_actions','user_logged_out',$this->request->session()->read('Auth.User.id'),['type' => 'Users','email' => $this->request->session()->read('Auth.User.email')]);
        return $this->redirect($this->Auth->logout());
    }

    public function account() {
        
        $id = $this->request->session()->read('Auth.User.id');
        $this->Users->id = $id;
        $user = $this->Users->get($id);
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);

        $this->loadModel('UserContactMethods');
        $this->paginate = [
            'conditions' => ['user_id' => $id]
        ];
        $userContactMethods = $this->paginate($this->UserContactMethods);

        $this->set(compact('userContactMethods'));
        $this->set('_serialize', ['userContactMethods']);
        
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->request->data['password']==$this->request->data['password_confirm']) {
                $user = $this->Users->patchEntity($user, $this->request->data);
                if ($this->Users->save($user)) {
                    $session = $this->getRequest()->getSession();
                    $session->write('Auth.User.show_metric', $this->request->data['show_metric']);
                    $session->write('Auth.User.email', $this->request->data['email']);
                    $session->write('Auth.User.name', $this->request->data['name']);
                    $this->Flash->success(__('Your account has been updated.'));
                } else {
                    $this->Flash->error(
                        __('The user could not be saved. Please, try again.')
                    );
                }
            } else {
                $this->Flash->error(__('Your passwords do not match.'));
            }
        } else {
            unset($user['password']);
        }
    }

    function reset() {
        # Display the reset form
        $this->set('hideSidebar',true);
        $this->set('hideHeader',true);
        $this->set('hideFooter',true);
        $this->set('bodyClass','login');
    }

    function password($token=null) {
        if(!$token) {
            // We have no token
            if (!empty($this->request->getData('email'))) {
                $user = $this->Users->findByEmail($this->request->getData('email'))->first();
                if(isset($user['id'])) {
                    // But we do have post data. Send the token in an email.
                    $token=sha1(date("h:i:s"));
                    $user['email_token']=$token;
                    $this->Users->save($user);

                    $Email = new Email();

                    $Email->viewVars(array('token' => $token,
                        'user' => $user));

                    $Email->template('reset_password', 'default')
                        ->emailFormat('html')
                        ->subject('Grownetics Password Reset')
                        ->to($user['email']);
                    try {
                        if($Email->send()) {
                            $this->Flash->success(
                                __("Password reset initiated. Check your email in a few minutes.")
                            );
                            return $this->redirect('/users/login');
                        } else {
                            $this->Flash->error("An error ocurred while sending the reset email. Please contact us for assistance.");
                            return $this->redirect('/users/login');
                        }
                    } catch (Exception $e) {
                        $this->Flash->error("An error ocurred while sending the reset email. Please contact us for assistance.");
                        return $this->redirect('/users/login');
                    }
                } else {
                    # We return the same for failure, otherwise we could leak the fact that certain emails exist
                    $this->Flash->success("Password reset initiated. Check your email in a few minutes.");
                    return $this->redirect('/users/login');
                }
            } else {
                # Display the reset form
            }
        } else {
            // We have a token
            if (!empty($this->request->getData())) {
                // We have form data
                // Update user, log them in.
                if ($this->request->getData('password') == $this->request->getData('password_confirm')) {
                    // This was a hack just to get one user working.
                    $user = $this->Users->findByEmailToken($token)->first();
                    if (!$user) {
                        $this->Flash->error("This reset code has expired.");
                        return $this->redirect('/users/login');
                    }
                    #dd($user);
                    $user->email_token = '';
                    $user->password = $this->request->getData('password');

                    if ($this->Users->save($user)) {
                        $this->Flash->success("Password updated successfully!");
                        return $this->redirect('/users/login');
                    } else {
                        $this->Flash->error("There was an error updating your password. Please contact us for assistance.");
                        return $this->redirect('/users/login');
                    }
                } else {
                    $this->Flash->error("Your passwords must match.");
                    $this->set('hideSidebar',true);
                }
            } else {
                // Render form
                $user = $this->Users->findByEmailToken($token);
                if ($user) {
                    $this->set('hideSidebar',true);
                } else {
                    $this->Flash->error("This reset code has expired.");
                    return $this->redirect('/users/login');
                }
            }
        }
        $this->set('hideSidebar',true);
        $this->set('hideHeader',true);
        $this->set('hideFooter',true);
        $this->set('bodyClass','login');
    }

    /*

    User management functions

    */

    public function index() {
        # If user is not an admin, only load non-admin users
        if ($this->Auth->user('role_id') > 1 ) {
            $query = $this->Users->find('all', ['contain' => ['Roles']])->where(['role_id >'=>1]);
        } else {
            $query = $this->Users->find('all', ['contain' => ['Roles']]);
        }
        $this->set('users', $this->paginate($query));
    }

    public function add() {
        if ($this->request->is('post')) {

            $user = $this->Users->findByEmail($this->request->data['email'])->first();
            if ($user) {
                $this->Flash->error(__('This email is already registered.'));
                return;
            }
            $siteSalt = 'cLJvJ2K}c#4mY7zp7s.mh';
            $token = substr(hash('ripemd160',$siteSalt . time() . uniqid() . $this->data['email']),0,10);
            $this->request->data['email_token'] = $token;

            $user = $this->Users->newEntity($this->request->data);
            $role = $this->Roles->findByLabel('user')->first();
            $user->role_id = $role->id;
            if ($this->Users->save($user)) {
                // Now add them to the invitee role

                $Email = new Email();

                $Email->viewVars(array('token' => $token,
                    'user' => $user));

                $Email->template('registration_invite', 'default')
                    ->emailFormat('html')
                    ->subject('Grownetics Password Reset')
                    ->to($user['email'])
                    ->from('support@grownetics.co');
                try {
                    if($Email->send()) {
                        $this->Flash->success(
                            __("Invite email sent. Have the user check their email shortly.")
                        );
                        return $this->redirect('/users/');
                    } else {
                        $this->Flash->error("An error ocurred while sending the invite email. Please contact us for assistance.");
                        return $this->redirect('/users/login');
                    }
                } catch (\Exception $e) {
                    /*
                     *  TODO:: Use as reminder only till we fix our email service
                     */
                    $this->Flash->success("Make sure to edit the newly created user and assign them a password!");

                    // $this->Flash->error("An error ocurred while sending the invite email. Please contact us for assistance.");
                    return $this->redirect('/users/login');
                }
            } else {
                $this->Flash->error(
                    __('The user could not be saved. Please, try again.')
                );
            }
        }
    }

    public function edit($id = null) {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);
        # User is not an admin, make sure the user they are trying to edit is also not an admin.
        if ($this->Auth->user('role_id') > 1 && $user['role_id'] == 1) {
            $this->Flash->error(__('User not found.'));
            return $this->redirect('/users/');
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->loadModel('Roles');
        if ($this->Auth->user('role_id') > 1) {
            # User is not an admin, don't show the admin role as an option.
            $roles = $this->Roles->find('list', ['limit' => 200,'conditions'=>['id !='=>1]]);
        } else {
            $roles = $this->Roles->find('list', ['limit' => 200]);
        }
        $this->set(compact('user', 'roles'));
        $this->set('_serialize', ['user']);
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        # User is not an admin, make sure the user they are trying to edit is also not an admin.
        if ($this->Auth->user('role_id') > 1 && $user['role_id'] == 1) {
            $this->Flash->error(__('User not found.'));
            return $this->redirect('/users/');
        }
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}