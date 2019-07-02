<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
?>
<div class="roles view large-9 medium-8 columns content">
    <h3><?= h($role->label) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Label') ?></th>
            <td><?= h($role->label) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($role->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($role->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($role->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Deleted Date') ?></th>
            <td><?= h($role->deleted_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Deleted') ?></th>
            <td><?= $role->deleted ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Users') ?></h4>
        <?php if (!empty($role->users)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Username') ?></th>
                <th scope="col"><?= __('Password') ?></th>
                <th scope="col"><?= __('Email') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Name') ?></th>
                <th scope="col"><?= __('Company') ?></th>
                <th scope="col"><?= __('Address') ?></th>
                <th scope="col"><?= __('Address 2') ?></th>
                <th scope="col"><?= __('City') ?></th>
                <th scope="col"><?= __('State') ?></th>
                <th scope="col"><?= __('Country') ?></th>
                <th scope="col"><?= __('Zip') ?></th>
                <th scope="col"><?= __('Access Code') ?></th>
                <th scope="col"><?= __('Email Token') ?></th>
                <th scope="col"><?= __('Dashboard Config') ?></th>
                <th scope="col"><?= __('Deleted') ?></th>
                <th scope="col"><?= __('Deleted Date') ?></th>
                <th scope="col"><?= __('Role Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($role->users as $users): ?>
            <tr>
                <td><?= h($users->id) ?></td>
                <td><?= h($users->username) ?></td>
                <td><?= h($users->password) ?></td>
                <td><?= h($users->email) ?></td>
                <td><?= h($users->created) ?></td>
                <td><?= h($users->modified) ?></td>
                <td><?= h($users->name) ?></td>
                <td><?= h($users->company) ?></td>
                <td><?= h($users->address) ?></td>
                <td><?= h($users->address_2) ?></td>
                <td><?= h($users->city) ?></td>
                <td><?= h($users->state) ?></td>
                <td><?= h($users->country) ?></td>
                <td><?= h($users->zip) ?></td>
                <td><?= h($users->access_code) ?></td>
                <td><?= h($users->email_token) ?></td>
                <td><?= h($users->dashboard_config) ?></td>
                <td><?= h($users->deleted) ?></td>
                <td><?= h($users->deleted_date) ?></td>
                <td><?= h($users->role_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Users', 'action' => 'view', $users->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'edit', $users->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Users', 'action' => 'delete', $users->id], ['confirm' => __('Are you sure you want to delete # {0}?', $users->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Acls') ?></h4>
        <?php if (!empty($role->acls)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Deleted') ?></th>
                <th scope="col"><?= __('Deleted Date') ?></th>
                <th scope="col"><?= __('Controller') ?></th>
                <th scope="col"><?= __('Action') ?></th>
                <th scope="col"><?= __('Rule') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($role->acls as $acls): ?>
            <tr>
                <td><?= h($acls->id) ?></td>
                <td><?= h($acls->created) ?></td>
                <td><?= h($acls->modified) ?></td>
                <td><?= h($acls->deleted) ?></td>
                <td><?= h($acls->deleted_date) ?></td>
                <td><?= h($acls->controller) ?></td>
                <td><?= h($acls->action) ?></td>
                <td><?= h($acls->rule) ?></td>
                <td><?= h($acls->user_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Acls', 'action' => 'view', $acls->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Acls', 'action' => 'edit', $acls->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Acls', 'action' => 'delete', $acls->id], ['confirm' => __('Are you sure you want to delete # {0}?', $acls->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php
$this->Form->resetTemplates();
echo $this->element('actionsMenu',['label'=>'Actions','actions'=>[
        $this->Html->link(__('Edit Role'), ['action' => 'edit', $role->id]),
        $this->Form->postLink(__('Delete Role'), ['action' => 'delete', $role->id], ['confirm' => __('Are you sure you want to delete # {0}?', $role->id)]),
        $this->Html->link(__('List Roles'), ['action' => 'index']),
        $this->Html->link(__('New Role'), ['action' => 'add']),
<a href="/users">List Users</a><a href="/users/add">New User</a><a href="/acls">List Acls</a><a href="/acls/add">New Acl</a>
]])?>
