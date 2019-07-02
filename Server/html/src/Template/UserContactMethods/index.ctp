<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UserContactMethod[]|\Cake\Collection\CollectionInterface $userContactMethods
 */
?>
<div class="userContactMethods index large-9 medium-8 columns content">
    <h3><?= __('User Contact Methods') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                <th scope="col"><?= $this->Paginator->sort('value') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($userContactMethods as $userContactMethod): ?>
            <tr>
                <td><?= $this->Number->format($userContactMethod->id) ?></td>
                <td><?= $userContactMethod->has('user') ? $this->Html->link($userContactMethod->user->name, ['controller' => 'Users', 'action' => 'view', $userContactMethod->user->id]) : '' ?></td>
                <td><?= h($userContactMethod->created);?>
                </td>
                <td><?= h($userContactMethod->modified);?>
                </td>
                <td><?= $this->Number->format($userContactMethod->type) ?></td>
                <td><?= h($userContactMethod->value);?>
                </td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $userContactMethod->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $userContactMethod->id]); 
                    $this->Form->resetTemplates(); ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $userContactMethod->id], ['confirm' => __('Are you sure you want to delete # {0}?', $userContactMethod->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>

<?=$this->element('actionsMenu',
        [
            'label'=>'Actions',
            'actions'=>[
                $this->Html->link(__('New User Contact Method'), ['action' => 'add']),
                           ]
        ]
    );
?>
