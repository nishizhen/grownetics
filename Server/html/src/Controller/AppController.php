<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Core\Configure;
use \Rollbar\Rollbar;
use App\Lib\SystemEventRecorder;
// use Muffin\Footprint\Auth\FootprintAwareTrait;
use Cake\ORM\TableRegistry;

use Cake\Cache\Cache;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 * @property \App\Model\Table\UsersRolesTable $UsersRoles
 * @property \App\Model\Table\RolesTable $Roles
 * @property \App\Model\Table\AclsTable $Acls
 * @property \App\Model\Table\RulesTable $Rules
 * @property \App\Model\Table\NotificationsTable $Notifications
 */
class AppController extends Controller
{
    // use FootprintAwareTrait;

    public $helpers = ['AssetCompress.AssetCompress', 'Cache', 'FeatureFlags.FeatureFlags'];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if (!env('DEV')) {
            Rollbar::init(
                array(
                    'access_token' => '8bfa8059c4534b8ea6978e8ae9d331d5',
                    'environment' => env('ENVIRONMENT')
                )
            );
        }

        $this->loadComponent('FeatureFlags');

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash', [
            'flash' => [
                'element' => 'auth_custom'
            ]
        ]);
        $this->loadComponent('Cookie');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ],
                'Xety/Cake3CookieAuth.Cookie'
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'authorize' => ['Controller']
        ]);

        $this->Auth->setConfig('authenticate', [
            'Form' => ['userModel' => 'Users']
        ]);

        // Allow the display action so our pages controller
        // continues to work.
        $this->Auth->allow(['raw', 'reboot', 'count', 'out', 'password', 'reset', 'aldc', 'tlc', 'chat', 'map', 'hdd', 'appdb', 'growpulse', 'system', 'login']);
    }

    public function beforeFilter(Event $event)
    {
        if ($this->request->getParam('action') == "raw" || $this->request->getParam('action') == "tlc") {
            # Don't try and login api devices.
            return true;
        }
        // Automatically Login.
        if (!$this->Auth->user() && $this->Cookie->read('CookieAuth')) {
            $this->loadModel('Users');
            $cookie = $this->Cookie->read('CookieAuth');
            $user = $this->Users->find('all', array(
                'conditions' => [
                    'email' => $cookie['username']
                ]
            ));

            if ($user->first() && $cookie['token'] == $user->first()->access_code) {
                if ($this->Auth->isAuthorized($user->first(), $this->request)) {
                    $this->Auth->setUser($user->first());
                    $recorder = new SystemEventRecorder();
                    $recorder->recordEvent('user_actions', 'user_logged_in', 1, ['type' => 'Cookie', 'email' => $cookie['username']]);
                    if ($this->FeatureFlags->getFlagValue("home_screen")) {
                        if ($this->Auth->redirectUrl() && $this->Auth->redirectUrl() != "/") {
                            return $this->redirect($this->Auth->redirectUrl());
                        } else {
                            return $this->redirect("/pages/home");
                        }
                    } else {
                        return $this->redirect($this->Auth->redirectUrl());
                    }
                } else {
                    $this->Cookie->delete('CookieAuth');
                }
            }
        }
    }

    public function isAuthorized($user)
    {
        $authed = false;
        if (isset($user['id'])) {
            $this->loadModel('UsersRoles');
            $this->loadModel('Roles');

            $usersRoles = $this->UsersRoles->findByUserId($user['id']);
            $navAcls = [];

            if ($usersRoles) {
                foreach ($usersRoles as $userRole) {
                    $role = $this->Roles->get($userRole->role_id);
                    $roleLabel = $role->label;
                    $this->set('navRole', $roleLabel);
                    if ($roleLabel === 'Admin') {
                        $authed = true;
                    }
                    // Everything other than an admin, look up the actual ACLs for this request.
                    $this->loadModel('Acls');

                    $controller = strtolower($this->request->params['controller']);
                    $action = strtolower($this->request->params['action']);
                    $query = $this->Acls->find('all');
                    $acls = [];
                    $query->matching('Roles', function ($query) use ($role) {
                        return $query->where(['Roles.id' => $role->id]);
                    });
                    $query->cache('acls-for-role-' . $role->id, 'acls');

                    foreach ($query as $row) {
                        $acls[] = $row; // $row->controller.'/'.$row->action;
                        if (
                            ($row->controller == $controller && ($row->action == $action || $row->action == '*'))
                            // &&
                            // strtolower($row->rule) == 'allow'
                        ) {
                            $authed = true;
                        }
                    }
                    // add all the acls to the response so we can dynamically generate nav menu
                    $navAcls = array_merge($navAcls, $acls);
                }
            } else {
                // This user has no Role! Redirect to the account page.
                return $this->redirect('/users/account');
            }
            $this->set('navAcls', $navAcls);
        }
        // Default deny
        return $authed;
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {

        if (
            !array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        } else {
            $this->set('BUILD_ID', Configure::read('BUILD_ID'));
            $this->set('BUILD_DATE', Configure::read('BUILD_DATE'));
            $this->set('bodyClass', strtolower($this->request->params['controller'] . ' ' . $this->request->params['action']));
        }
    }

    public function notification($opts)
    {
        $this->loadModel('RuleActions');
        $this->loadModel('Notifications');

        $message = '';
        if (isset($opts['message'])) {
            $message = env('FACILITY_ID') . '-' . env('FACILITY_NAME') . ' ' . $opts['message'];
        }
        $template = '';
        if (isset($opts['template'])) {
            $template = $opts['template'];
        }
        $user_id = 0;
        if (isset($opts['user_id'])) {
            $user_id = $opts['user_id'];
        }
        $source_type = null;
        if (isset($opts['source_type'])) {
            $source_type = $opts['source_type'];
        }
        $source_id = null;
        if (isset($opts['source_id'])) {
            $source_id = $opts['source_id'];
        }
        $notification = $this->Notifications->newEntity([
            'status' => 0,
            'message' => $message,
            'template' => $template,
            'user_id' => $user_id,
            'source_type' => $source_type,
            'source_id' => $source_id,
            'notification_level' => isset($opts['notification_level']) ? $this->RuleActions->enumValueToKey('notification_level', $opts['notification_level']) : $this->RuleActions->enumValueToKey('notification_level', 'Dashboard Notification')
        ]);
        $this->Notifications->save($notification);
    }
}
