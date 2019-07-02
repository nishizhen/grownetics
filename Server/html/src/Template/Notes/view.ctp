<div class="notes view large-9 medium-8 columns content">
    <h3><?= h($note->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $note->has('user') ? $this->Html->link($note->user->name, ['controller' => 'Users', 'action' => 'view', $note->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Cultivar') ?></th>
            <td><?= $note->has('cultivar') ? $this->Html->link($note->cultivar->label, ['controller' => 'Cultivars', 'action' => 'view', $note->cultivar->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Zone') ?></th>
            <td><?= $note->has('zone') ? $this->Html->link($note->zone->label, ['controller' => 'Zones', 'action' => 'view', $note->zone->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($note->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Batch Id') ?></th>
            <td><?= $this->Number->format($note->batch_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($note->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($note->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Deleted') ?></th>
            <td><?= h($note->deleted) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Note') ?></h4>
        <?= $this->Text->autoParagraph(h($note->note)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Photos') ?></h4>
        <?php if (!empty($note->photos)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Note Id') ?></th>
                <th scope="col"><?= __('Photo Blob') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Deleted') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($note->photos as $photos): ?>
            <tr>
                <td><?= h($photos->id) ?></td>
                <td><?= h($photos->note_id) ?></td>
                <td><?= h($photos->photo_blob) ?></td>
                <td><?= h($photos->created) ?></td>
                <td><?= h($photos->modified) ?></td>
                <td><?= h($photos->deleted) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Photos', 'action' => 'view', $photos->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Photos', 'action' => 'edit', $photos->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Photos', 'action' => 'delete', $photos->id], ['confirm' => __('Are you sure you want to delete # {0}?', $photos->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Plants') ?></h4>
        <?php if (!empty($note->plants)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Plant Id') ?></th>
                <th scope="col"><?= __('Short Plant Id') ?></th>
                <th scope="col"><?= __('Zone Id') ?></th>
                <th scope="col"><?= __('Map Item Id') ?></th>
                <th scope="col"><?= __('Status') ?></th>
                <th scope="col"><?= __('Harvest Batch Id') ?></th>
                <th scope="col"><?= __('Recipe Id') ?></th>
                <th scope="col"><?= __('Deleted') ?></th>
                <th scope="col"><?= __('Wet Whole Weight') ?></th>
                <th scope="col"><?= __('Wet Waste Weight') ?></th>
                <th scope="col"><?= __('Wet Whole Defoliated Weight') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($note->plants as $plants): ?>
            <tr>
                <td><?= h($plants->id) ?></td>
                <td><?= h($plants->plant_id) ?></td>
                <td><?= h($plants->short_plant_id) ?></td>
                <td><?= h($plants->zone_id) ?></td>
                <td><?= h($plants->map_item_id) ?></td>
                <td><?= h($plants->status) ?></td>
                <td><?= h($plants->harvest_batch_id) ?></td>
                <td><?= h($plants->recipe_id) ?></td>
                <td><?= h($plants->deleted) ?></td>
                <td><?= h($plants->wet_whole_weight) ?></td>
                <td><?= h($plants->wet_waste_weight) ?></td>
                <td><?= h($plants->wet_whole_defoliated_weight) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Plants', 'action' => 'view', $plants->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Plants', 'action' => 'edit', $plants->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Plants', 'action' => 'delete', $plants->id], ['confirm' => __('Are you sure you want to delete # {0}?', $plants->id)]) ?>
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
        $this->Html->link(__('Edit Note'), ['action' => 'edit', $note->id]),
        $this->Form->postLink(__('Delete Note'), ['action' => 'delete', $note->id], ['confirm' => __('Are you sure you want to delete # {0}?', $note->id)]),
        $this->Html->link(__('List Notes'), ['action' => 'index']),
        $this->Html->link(__('New Note'), ['action' => 'add']),
//<a href="/users">List Users</a><a href="/users/add">New User</a><a href="/cultivars">List Cultivars</a><a href="/cultivars/add">New Cultivar</a><a href="/zones">List Zones</a><a href="/zones/add">New Zone</a><a href="/photos">List Photos</a><a href="/photos/add">New Photo</a><a href="/plants">List Plants</a><a href="/plants/add">New Plant</a>
]])?>
