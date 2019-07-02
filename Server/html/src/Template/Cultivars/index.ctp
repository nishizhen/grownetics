<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cultivar[]|\Cake\Collection\CollectionInterface $cultivars
 */
?>
<div class="cultivars index">
    <h3><?= __('Cultivars') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('label', 'Name') ?></th>
                <th><?= $this->Paginator->sort('batch_count') ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $this->Form->resetTemplates();
            foreach ($cultivars as $cultivar): ?>
            <tr>
                <td><?= $this->Html->link($cultivar->label, ['controller' => 'Cultivars', 'action' => 'view', $cultivar->id]) ?></td>
                <td><?= h($cultivar->batch_count) ?></td>
                <td><?= $this->Form->postLink(__("<button class='fa fa-trash btn-xs btn btn-danger'></button>"), ['action' => 'delete', $cultivar->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete cultivar {0}?', $cultivar->label)])
                ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>

<?=$this->Html->link(__('New Cultivar'), ['action' => 'add'],['class'=>'btn btn-sm btn-theme03'])?>
