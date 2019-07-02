<?php
/**
 * Chat Controller
 */
namespace App\Controller;

class ChatsController extends AppController
{

    public function chat() {
        $response = array('status'=>'failed');
        $chat = $this->Chats->newEntity();
        if ($this->request->is('post')) {
            // print_r($this->request->data); die();
            $data = json_decode($this->request->data[0], true);
            
            $chat = $this->Chats->patchEntity($chat, $data);
            if ($this->Chats->save($chat)) {
                $this->loadModel('Users');
                $user = $this->Users->get($chat->user_id);
                $avatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email))) . '?d=identicon';
                $response = array('status'=>'success','avatar'=>$avatar);
            }
        }
        $this->set(compact('response'));
        $this->set('_serialize', ['response']);
    }
}