<div class="organizations index large-9 medium-8 columns content">
    <h3><?= __('Organizations') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('label') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $this->Form->resetTemplates();
            foreach ($organizations as $organization):
                $pendingInvite = FALSE;
                foreach ($orgUserRoles as $role) {
                    if (
                        $role->organization_id == $organization->id
                        &&
                        $role->role_id == $inviteeRoleId
                    ) {
                        $pendingInvite = TRUE;
                    }
                }
                ?>
                <tr>
                    <td>
                        <?php if ($pendingInvite) {
                            echo $organization->label;
                        } else { ?>
                            <?= $this->Html->link($organization->label, ['action' => 'view', $organization->id]) ?>
                            <?= $this->element('editBtn', ['url' => '/organizations/edit/' . h($organization->id)]); ?>
                        <?php } ?>
                    </td>
                    </td>
                    <td class="actions">
                        <?php if (!$pendingInvite) { ?>
                            <?= $this->Html->link(__('View'), ['action' => 'view', $organization->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $organization->id]); ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $organization->id], ['confirm' => __('Are you sure you want to delete # {0}?', $organization->id)]) ?>
                        <?php } else { ?>
                            <?= $this->Form->postLink(__('Accept Invite'), ['action' => 'respondToInvite', $organization->id, 1], ['confirm' => __('Are you sure you want to join {0}?', $organization->label)]); ?>
                            <?= $this->Form->postLink(__('Delete Invite'), ['action' => 'respondToInvite', $organization->id, 0], ['confirm' => __('Are you sure you want to delete your invitation to {0}?', $organization->label)]); ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->element('paginator') ?>
</div>

<?= $this->element(
    'actionsMenu',
    [
        'label' => 'Actions',
        'actions' => [
            $this->Html->link(__('New Organization'), ['action' => 'add']),

        ]
    ]
);
?>