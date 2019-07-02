<div class="setPoints index large-9 medium-8 columns content">
    <h3><?= __('Set Points') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('label') ?></th>
                <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('value') ?></th>
                <th scope="col"><?= $this->Paginator->sort('target_type') ?></th>
                <th scope="col"><?= $this->Paginator->sort('target_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('data_type') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($setPoints as $setPoint): ?>
            <tr>
                <td><?= h($setPoint->label);?>
                     <?=$this->element('editBtn',['url'=>'/setPoints/edit/'.h($setPoint->id)]);?>
                </td>
                <td><?= $this->Number->format($setPoint->status) ?></td>
                <td><?= h($setPoint->value);?>
                </td>
                <td><?= $this->Number->format($setPoint->target_type) ?></td>
                <td><?= $this->Number->format($setPoint->target_id) ?></td>
                <td><?= $this->Number->format($setPoint->data_type) ?></td>
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
                $this->Html->link(__('New Set Point'), ['action' => 'add']),

            ]
        ]
    );
?>
