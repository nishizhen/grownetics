<?php $this->Form->resetTemplates(); ?>
<div class="organizations view large-9 medium-8 columns content">
    <h3><?= h($organization->label) ?></h3>
    <div class="related">
        <h4><?= __('Organization Members') ?></h4>
        <?php
        $this->Form->resetTemplates();
        if (!empty($organization->users_roles)) : ?>
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th scope="col"><?= __('User Id') ?></th>
                    <th scope="col"><?= __('Role Id') ?></th>
                </tr>
                <?php

                $isAdmin = false;
                foreach ($userRoles as $role) {
                    if (
                        $role->organization_id == $organization->id
                        &&
                        $role->role_id == $orgAdminRoleId
                    ) {
                        $isCurrentUserOrganizationAdmin = true;
                    }
                }

                foreach ($organization->users_roles as $usersRoles) : ?>
                    <tr>
                        <td><?= h($usersRoles->user->name) ?></td>
                        <td><?php
                            echo h($usersRoles->role->label);
                            echo "&nbsp;";

                            $actions = [];
                            if ($isCurrentUserOrganizationAdmin) {
                                if ($usersRoles->role->label == "Organization Admin") {
                                    if ($adminCount > 1) {
                                        $actions[] = $this->Form->postLink("Downgrade Admin to Member", ['controller' => 'Organizations', 'action' => 'setUserRole', $organization->id, $usersRoles->user->id, $orgMemberRoleId], ['confirm' => __('Are you sure you want to remove "' . $usersRoles->user->name . '" as an admin of this Organization?')]);
                                    }
                                } else {
                                    $actions[] = $this->Form->postLink("Make User Admin", ['controller' => 'Organizations', 'action' => 'setUserRole', $organization->id, $usersRoles->user->id, $orgAdminRoleId], ['confirm' => __('Are you sure you want to make "' . $usersRoles->user->name . '" an admin of this Organization?')]);
                                }
                            }
                            if (count($actions)) {
                                echo $this->element('actionsMenu', ['label' => '', 'actions' => $actions]);
                            }

                            ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="strains form row mt">
    <div class="col-lg-12">
        <div class="form-panel">
            <h4 class="mb"><i class="fa fa-angle-right"></i>Add User</h4>
            <?php
            $this->Form->resetTemplates();
            echo $this->Form->create(null, [
                'class' => 'form-horizontal style-form',
                'url' => '/organizations/addUser/' . $organization->id
            ]); ?>
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-2 col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                        <?= $this->Form->input(
                            'email',
                            array(
                                'class' => 'form-control round-form',
                                'label' => false,

                            )
                        ); ?>
                    </div>
                </div>
            </fieldset>
            <?php echo $this->Form->submit('Send Invite', array('class' => 'btn btn-theme'));
            echo $this->Form->end(); ?>
        </div>
    </div>
</div>

<?= $this->element('actionsMenu', ['label' => 'Actions', 'actions' => [
    $this->Html->link(__('Edit Organization'), ['action' => 'edit', $organization->id]),
    $this->Form->postLink(__('Delete Organization'), ['action' => 'delete', $organization->id], ['confirm' => __('Are you sure you want to delete # {0}?', $organization->id)]),
    $this->Html->link(__('List Organizations'), ['action' => 'index']),
    $this->Html->link(__('New Organization'), ['action' => 'add']),
]]) ?>