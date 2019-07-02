<?= $this->Html->script('harvestbatches/add', ['block' => 'scriptBottom']); ?>


<div class="harvestBatches form large-9 medium-8 columns content">
    <?= $this->Form->create($harvestBatch, ['templateVars'=>['header'=>'New Harvest Batch'], 'id'=>'harvestBatchForm']) ?>
    <fieldset>
        <?php
            echo $this->Form->input('cultivar_id', ['options' => $cultivars]);
        ?>
        <div class="form-group">
        <?php
            echo $this->Form->label('planted_date');
            echo $this->Form->datepicker('planted_date', ['required'=>true, 'label' => 'Plant Date', 'dateFormat' => 'yy-mm-dd']);
            ?>
        </div>
        <?php
            echo $this->Form->input('recipe_id', ['options' => $recipes]);
        ?>
        <div id="recipe-entries">
            <ul>
                <li>
                    <table class="recipeForm">
                    Select the Room then Group for the batch to travel through:
                    <i style = "position:relative; display: none;" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i>
                        <tr>
                            <th>
                            </th>
                            <th>
                                Room
                            </th>
                            <th>
                                Bench
                            </th>
                        </tr>
                    <?php foreach ($recipeEntries as $entry): ?>
                        <tr>
                            <td>
                            <?= $this->Enum->enumKeyToValue('Zones', 'plant_zone_types', $entry->plant_zone_type_id) ?>
                            </td>
                            <td>
                                <div class="form-group ui search normal selection dropdown roomDDown">
                                    <input type="hidden" name="room_ids[]" form="harvestBatchForm" required>
                                    <i class="dropdown icon"></i>
                                    <div class="default text">Select a Room...</div>
                                    <div class="menu roomMenu">
                                    <?php foreach ($entry->room_data as $zone): ?>
                                        <option name="zones[]" class="item" data-value="<?=$zone->id?>" value="<?=$zone->id?>">
                                            <?=$zone->label?>
                                        </option>
                                    <?php endforeach; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="form-group ui search normal selection dropdown groupDDown" >
                                    <input type="hidden" name="group_ids[]" form="harvestBatchForm" required>
                                    <i class="dropdown icon"></i>
                                    <div class="text" data-value="0">Auto-fill</div>
                                    <div class="menu groupMenu">
                                    <option class='item' data-value='0' value='0'>Auto-fill</option>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </table>
                </li>
            </ul>
        </div>
        <?php
            echo $this->Form->input('start id', ['placeholder' => 'Leave blank to add Plants later']);
            echo $this->Form->input('end id', ['placeholder' => 'Leave blank to add Plants later']);
        ?>
        <div>Enter Individual ID's:</div>
        <div class="form-group">
            <div class="col-sm-12">
                <?= $this->Form->textarea('plant_list', ['id' => 'plant_list','class' => 'form-control', 'placeholder' => '1AF001, 1AF002, 1AF003, ...']); ?>
            <span class="help-block">Add Plant ID's seperated by commas.</span>
            </div>
        </div>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>