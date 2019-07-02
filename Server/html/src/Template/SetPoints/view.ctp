<div class="setPoints view large-9 medium-8 columns content">
    <h3><?= h($setPoint->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Label') ?></th>
            <td><?= h($setPoint->label) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Value') ?></th>
            <td><?= h($setPoint->value) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($setPoint->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= $this->Number->format($setPoint->status) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Target Type') ?></th>
            <td><?= $this->Number->format($setPoint->target_type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Target Id') ?></th>
            <td><?= $this->Number->format($setPoint->target_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Data Type') ?></th>
            <td><?= $this->Number->format($setPoint->data_type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($setPoint->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($setPoint->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Deleted') ?></th>
            <td><?= h($setPoint->deleted) ?></td>
        </tr>
    </table>
</div>
<?php
$this->Form->resetTemplates();
echo $this->element('actionsMenu',['label'=>'Actions','actions'=>[
        $this->Html->link(__('Edit Set Point'), ['action' => 'edit', $setPoint->id]),
        $this->Form->postLink(__('Delete Set Point'), ['action' => 'delete', $setPoint->id], ['confirm' => __('Are you sure you want to delete # {0}?', $setPoint->id)]),
        $this->Html->link(__('List Set Points'), ['action' => 'index']),
        $this->Html->link(__('New Set Point'), ['action' => 'add']),

]])?>
