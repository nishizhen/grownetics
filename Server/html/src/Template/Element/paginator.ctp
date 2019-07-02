<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="paginator">
    <ul class="pagination">
	    <?= $this->Paginator->first('|< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >|') ?>
        <?= $this->Paginator->limitControl([],null,['label'=>'Results Per Page:','class'=>'form-control']); ?>
    </ul><br />
    <?= $this->Paginator->counter([
		'format' => 'Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total, starting on record {{start}}, ending on {{end}}'
	]) ?>
</div>