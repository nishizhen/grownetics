<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Chat cell
 */
class ChatCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @return void
     */
    public function display()
    {
        $this->loadModel('Chats');
        $params = [
            'order' => ['Chats.created' => 'asc'],
            'limit' => 50,
            'fields' => ['Chats.created','Chats.message'],
            'contain' => ['Users']
        ];
        $chats = $this->Chats->find('all',$params)
        ->contain([
            'Users' => function ($q) {
               return $q
                    ->select(['name','email']);
            }
        ]);
        $this->set('chats',$chats);
    }
}
