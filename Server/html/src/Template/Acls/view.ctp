<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Acl $acl
 */
?>
<div class="acls view large-9 medium-8 columns content">
    <h3><?= h($acl->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Controller') ?></th>
            <td><?= h($acl->controller) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Action') ?></th>
            <td><?= h($acl->action) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rule') ?></th>
            <td><?= h($acl->rule) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $acl->has('user') ? $this->Html->link($acl->user->name, ['controller' => 'Users', 'action' => 'view', $acl->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($acl->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($acl->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($acl->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Deleted Date') ?></th>
            <td><?= h($acl->deleted_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Deleted') ?></th>
            <td><?= $acl->deleted ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Roles') ?></h4>
        <?php if (!empty($acl->roles)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Deleted') ?></th>
                <th scope="col"><?= __('Deleted Date') ?></th>
                <th scope="col"><?= __('Label') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($acl->roles as $roles): ?>
            <tr>
                <td><?= h($roles->id) ?></td>
                <td><?= h($roles->created) ?></td>
                <td><?= h($roles->modified) ?></td>
                <td><?= h($roles->deleted) ?></td>
                <td><?= h($roles->deleted_date) ?></td>
                <td><?= h($roles->label) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Roles', 'action' => 'view', $roles->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Roles', 'action' => 'edit', $roles->id]);
                    $this->Form->resetTemplates(); ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Roles', 'action' => 'delete', $roles->id], ['confirm' => __('Are you sure you want to delete # {0}?', $roles->id)]) ?>
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
        $this->Html->link(__('Edit Acl'), ['action' => 'edit', $acl->id]),
        $this->Form->postLink(__('Delete Acl'), ['action' => 'delete', $acl->id], ['confirm' => __('Are you sure you want to delete # {0}?', $acl->id)]),
        $this->Html->link(__('List Acls'), ['action' => 'index']),
        $this->Html->link(__('New Acl'), ['action' => 'add']),
]])?>
