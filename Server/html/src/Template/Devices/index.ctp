<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Device[]|\Cake\Collection\CollectionInterface $devices
 */
?>
<div class="devices index">
    <h3><?php echo __('Devices'); ?></h3>
    <table cellpadding="0" cellspacing="0">
    <thead>
    <tr>
            <th><?php echo $this->Paginator->sort('ID'); ?></th>
            <th><?php echo $this->Paginator->sort('label'); ?></th>
            <th><?php echo $this->Paginator->sort('last_message'); ?></th>
            <?php if ($navRole == 'Admin') { ?>

                <th><?php echo $this->Paginator->sort('GrowFaker Requests'); ?></th>
                <th><?php echo $this->Paginator->sort('Mode'); ?>
                    <?php echo $this->GrowFaker->getMode(0)?>
                    <?=$this->element('actionsMenu',['actions'=>[
                        'Global Device Mode',
                        $this->Html->link(__('(None)'), array('action' => 'set_mode', 0, 0)),
                        $this->Html->link(__('Flat'), array('action' => 'set_mode', 0, 1)),
                        $this->Html->link(__('Random'), array('action' => 'set_mode', 0, 2)),
                        $this->Html->link(__('Drift'), array('action' => 'set_mode', 0, 3)),
                        $this->Html->link(__('Heat'), array('action' => 'set_mode', 0, 4)),
                        $this->Html->link(__('Cool'), array('action' => 'set_mode', 0, 5)),
                        $this->Html->link(__('Dead'), array('action' => 'set_mode', 0, 6)),
                        $this->Html->link(__('Demo'), array('action' => 'set_mode', 0, 7)),
                        $this->Html->link(__('Sketchy'), array('action' => 'set_mode', 0, 8)),
                    ]])?></th>
            <?php } ?>
            <th><?php echo $this->Paginator->sort('version'); ?></th>
            <th><?php echo $this->Paginator->sort('refresh_rate'); ?></th>
            <th><?php echo $this->Paginator->sort('reboot_rate'); ?></th>
            <th><?php echo $this->Paginator->sort('status'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?=$this->Form->resetTemplates();?>
    <?php foreach ($devices as $device):
        $time_since_good_data = '';
        $cachedDevice = $this->Cache->get('device-'.$device['id']);
        if (isset($cachedDevice['last_message'])) {
            $time_since_good_data = $this->Time->timeAgoInWords($cachedDevice['last_message']);
        }
    ?>
    <tr>
        <td><a href='/devices/view/<?php echo h($device['id']); ?>'><?php echo h($device['id']); ?></a></td>
        <td><a href='/devices/view/<?php echo h($device['id']); ?>'><?=$device['label'];?></a> <?=$this->element('editBtn',['url'=>'/devices/edit/'.h($device['id'])])?></td>
        <td><?php
        echo $time_since_good_data; ?>&nbsp;</td>

        <?php if ($role == 'Admin') { ?>
            <td><?php echo $this->GrowFaker->getRequests($device['id'])?></td>
            <td><?php echo $this->GrowFaker->getMode($device['id'])?>
                <?=$this->element('actionsMenu',['actions'=>[
                    $this->Html->link(__('(None)'), array('action' => 'set_mode', $device['id'], 0)),
                    $this->Html->link(__('Flat'), array('action' => 'set_mode', $device['id'], 1)),
                    $this->Html->link(__('Random'), array('action' => 'set_mode', $device['id'], 2)),
                    $this->Html->link(__('Drift'), array('action' => 'set_mode', $device['id'], 3)),
                    $this->Html->link(__('Heat'), array('action' => 'set_mode', $device['id'], 4)),
                    $this->Html->link(__('Cool'), array('action' => 'set_mode', $device['id'], 5)),
                    $this->Html->link(__('Dead'), array('action' => 'set_mode', $device['id'], 6)),
                    $this->Html->link(__('Demo'), array('action' => 'set_mode', $device['id'], 7)),
                    $this->Html->link(__('Sketchy'), array('action' => 'set_mode', $device['id'], 8)),
                ]])?>
            </td>
        <?php } ?>
        <td><?php isset($cachedDevice['version']) ? $cachedDevice['version'] : ''; ?>&nbsp;</td>
        <td><?php echo $device['refresh_rate']; ?>&nbsp;</td>
        <td><?php echo $device['reboot_rate']; ?>&nbsp;</td>
        <td><?php
            switch ($cachedDevice['status']) {
                    case 0: ?>
                <span class="label label-warning label-mini">Disabled</span>
                <?php	break;
                    case 1: ?>
                <span class="label label-success label-mini">Active</span>
                <?php
                break;
                    case 2: ?>
                <span class="label label-warning label-mini">Rebooting</span>
                <?php	break;
                    case 3: ?>
                <span class="label label-success label-mini">Active</span>
                <?php	break;
                case 4: ?>
                <span class="label label-warning label-mini">Offline</span>
                <?php	break;
            }
        ?>&nbsp;</td>
        <td class="actions">
            <?=$this->element('actionsMenu',['actions'=>[
                $this->Html->link(__('View'), array('action' => 'view', $device['id'])),
                $this->Html->link(__('Edit'), array('action' => 'edit', $device['id'])),
                $this->Html->link(__('Edit Zones'), array('action' => 'zones', $device['id'])),
                $this->Form->postLink(__("Delete"), ['action' => 'delete', $device->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete {0}?', $device->label)])
            ]])?>
            

        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>


<?=$this->element('actionsMenu',['label'=>'Actions','actions'=>[
    $this->Html->link(__('New Device'), array('action' => 'add'))
]])?>