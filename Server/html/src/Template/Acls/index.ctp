<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Acl[]|\Cake\Collection\CollectionInterface $acls
 */
?>
<div class="acls index large-9 medium-8 columns content">
    <h3><?= __('Acls') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('controller') ?></th>
                <th scope="col"><?= $this->Paginator->sort('action') ?></th>
                <th scope="col"><?= $this->Paginator->sort('rule') ?></th>
                <th scope="col">Roles</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($acls as $acl): ?>
            <tr>
                <td><?= $this->Html->link(__('View'), ['action' => 'view', $acl->id]) ?></td>
                <td><?= h($acl->controller);?>
                </td>
                <td><?= h($acl->action);?>
                </td>
                <td><?= h($acl->rule);?>
                </td>
                <td>
                    <?php if (!empty($acl->roles)): ?>
                            <?php foreach ($acl->roles as $roles): ?>
                                    <?= h($roles->label) ?>
                            <?php endforeach; ?>
                    <?php endif; ?>
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
                $this->Html->link(__('New Acl'), ['action' => 'add']),
            ]
        ]
    );
?>
