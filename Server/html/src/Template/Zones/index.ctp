<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Zone[]|\Cake\Collection\CollectionInterface $zones
 */
?>
<?php $this->Html->script('cell/set_point/SetPointSelector', ['block' => 'scriptBottom']); ?>
<div class="zones index">
    <h3><?= __('Zones') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('label') ?></th>
                <th scope="col"><i class="wi wi-humidity"></i> Humidity (%)</th>
                <th scope="col"><i class="wi wi-thermometer"></i> Temperature (<?=$tempSymbol?>)</th>
                <th scope="col"><i class="wi wi-raindrop"></i> Co2 (ppm)</th>
                <th scope="col"><i class="wi wi-lightning"></i>Light Output </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($zones as $zone): ?>
            <tr>
                <td><a href='/zones/view/<?php echo $this->Number->format($zone->id); ?>'><?= h($zone->label) ?></a> <?=$this->element('editBtn',['url'=>'/zones/edit/'.h($zone->id)])?></td>
                <td data-zone_id="<?=$zone->id?>" data-plant_zone_type_id="<?=$zone->plant_zone_type_id?>">
                    <?php
                    echo $this->Cache->get('zone-value-2-' . $zone->id);
                    if ($zone->bacnet_hum_set) {
                        echo $this->cell('SetPoint::humSetPointView', [$this->Enum->enumValueToKey('SetPoints', 'target_type', 'Zone'), $zone]);
                    }?>
                </td>
                <td data-zone_id="<?=$zone->id?>" data-plant_zone_type_id="<?=$zone->plant_zone_type_id?>">
                    <?php
                    echo $this->Converter->displayValue($this->Cache->get('zone-value-3-' . $zone->id),3);
                    if ($zone->bacnet_temp_set) {
                        echo $this->cell('SetPoint::tempSetPointView', [$this->Enum->enumValueToKey('SetPoints', 'target_type', 'Zone'), $zone, $this->Session->read('Auth.User.id')]);
                    }?>
                </td>
                <td data-zone_id="<?=$zone->id?>" data-plant_zone_type_id="<?=$zone->plant_zone_type_id?>">
                    <?php
                    echo $this->Cache->get('zone-value-4-' . $zone->id);
                    if ($zone->bacnet_hum_set) {
                        echo $this->cell('SetPoint::carbonSetPointView', [$this->Enum->enumValueToKey('SetPoints', 'target_type', 'Zone'), $zone]);
                    }?>
                </td>
                <td data-zone_id="<?=$zone->id?>" data-plant_zone_type_id="<?=$zone->plant_zone_type_id?>">
                    <?php
                    echo $this->Cache->get('zone-value-11-' . $zone->id);
                    if ($zone->light_level_set) {
                        echo $this->cell('SetPoint::lightsSetPointView', [$this->Enum->enumValueToKey('SetPoints', 'target_type', 'Zone'), $zone]);
                    }?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>
<?=$this->element('actionsMenu',['label'=>'Actions','actions'=>[
    $this->Html->link(__('New Zone'), ['action' => 'add']),
    $this->Html->link(__('List Outputs'), ['controller' => 'Outputs', 'action' => 'index']),
    $this->Html->link(__('New Output'), ['controller' => 'Outputs', 'action' => 'add']),
    $this->Html->link(__('List Sensors'), ['controller' => 'Sensors', 'action' => 'index']),
    $this->Html->link(__('New Sensor'), ['controller' => 'Sensors', 'action' => 'add'])
]])?>