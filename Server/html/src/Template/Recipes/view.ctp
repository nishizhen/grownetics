<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecipeEntry[]|\Cake\Collection\CollectionInterface $recipeEntries
 * @var \App\Model\Entity\Recipe $recipe
 */
echo $this->Html->script('recipes/recipesTable', ['block' => 'scriptBottom']);
?>
<div class="recipes view large-9 medium-8 columns content">
  <h3><?= h($recipe->label) ?></h3>
  <table class="vertical-table">
    <tr>
      <th scope="row"><?= __('Created') ?></th>
      <td><?= h($recipe->created) ?></td>
    </tr>
  </table>
</div>
<?php
$this->Form->resetTemplates();
echo $this->element('actionsMenu',['label'=>'Actions','actions'=>[
$this->Html->link(__('Edit Recipe'), ['action' => 'edit', $recipe->id]),
$this->Form->postLink(__('Delete Recipe'), ['action' => 'delete', $recipe->id], ['confirm' => __('Are you sure you want to delete {0}?', $recipe->label)]),
$this->Html->link(__('List Recipes'), ['action' => 'index']),
$this->Html->link(__('New Harvest Batch'), ['controller'=>'HarvestBatches', 'action' => 'add'])
]])?>

<div class="recipeEntries index large-9 medium-8 columns content">
  <h3><?= __('Recipe Entries') ?></h3>
  <table id="hidden-table-info" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th scope="col"><?= $this->Paginator->sort('zone_type_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('days') ?></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php $recipe_start = 0;
            foreach ($recipeEntries as $recipeEntry): ?>
        <?php if ($recipeEntry->recipe_id == $recipe->id): ?>          
          <tr>
            <td class="identifier" id="<?= $recipeEntry->plant_zone_type_id?>" name="<?= $this->Enum->enumKeyToValue('Zones','plant_zone_types',$recipeEntry->plant_zone_type_id).'-'.$recipeEntry->id?>"> <?= $this->Enum->enumKeyToValue('Zones','plant_zone_types',$recipeEntry->plant_zone_type_id); if ($recipeEntry->task_type_id) { echo ' - Task: ' . $this->Enum->enumKeyToValue('Tasks', 'types', $recipeEntry->task_type_id); } ?>
                <?=$this->element('editBtn',['url'=>'/recipeEntries/edit/'.h($recipeEntry['id'])])?></td>
            <td id="days_in_<?=$this->Enum->enumKeyToValue('Zones', 'plant_zone_types', $recipeEntry->plant_zone_type_id).'-'.$recipeEntry->id?>">
                <?php
                $new_start = $this->Number->format($recipe_start + $recipeEntry->days);
                echo $recipe_start . ' - ' . $new_start;
                $recipe_start = $new_start;
                ?>
            </td>
            <td class="actions">
              <?= $this->Form->postLink(__("<button class='fa fa-trash btn-xs btn btn-danger'></button>"), ['controller'=>'RecipeEntries','action' => 'delete', $recipeEntry->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete entry: {0} days in {1}?', $recipeEntry->days, $this->Enum->enumKeyToValue('Zones','plant_zone_types',$recipeEntry->plant_zone_type_id))]) ?>
            </td>
          </tr>
        <?php $previous_recipe = $recipeEntry; endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
  <form class="form-inline" role="form" action="/recipeEntries/add" method="post" style="display: inline-block !important;">
      <select name="plant_zone_type_id" class="form-control">
          <?php foreach($plant_zone_types as $plant_zone_type): ?>
            <option value="<?= $this->Enum->enumValueToKey('Zones', 'plant_zone_types', $plant_zone_type) ?>"><?=$plant_zone_type?></option>
          <?php  endforeach; ?>
      </select>
    <label class="sr-only" for="days_input">Days</label>
    <input name="days" type="number" class="form-control" id="days_input" placeholder="# of days in zone" required>
    <input type="hidden" name="recipe_id" value="<?=$recipe->id?>">
  <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i></button>
  </form>
  <?=$this->element('paginator')?>
</div>




