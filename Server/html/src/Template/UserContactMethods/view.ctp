<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UserContactMethod $userContactMethod
 */
?>
<div class="userContactMethods view large-9 medium-8 columns content">
    <h3><?= h($userContactMethod->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $userContactMethod->has('user') ? $this->Html->link($userContactMethod->user->name, ['controller' => 'Users', 'action' => 'view', $userContactMethod->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Value') ?></th>
            <td><?= h($userContactMethod->value) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($userContactMethod->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Type') ?></th>
            <td><?= $this->Number->format($userContactMethod->type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($userContactMethod->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($userContactMethod->modified) ?></td>
        </tr>
    </table>
</div>
<?php
$this->Form->resetTemplates();
echo $this->element('actionsMenu',['label'=>'Actions','actions'=>[
        $this->Html->link(__('Edit User Contact Method'), ['action' => 'edit', $userContactMethod->id]),
        $this->Form->postLink(__('Delete User Contact Method'), ['action' => 'delete', $userContactMethod->id], ['confirm' => __('Are you sure you want to delete # {0}?', $userContactMethod->id)]),
        $this->Html->link(__('List User Contact Methods'), ['action' => 'index']),
        $this->Html->link(__('New User Contact Method'), ['action' => 'add']),
<a href="/users">List Users</a><a href="/users/add">New User</a>
]])?>
