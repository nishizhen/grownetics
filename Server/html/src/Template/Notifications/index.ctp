<div class="notifications index columns content">
    <h3>
        Notifications
    </h3>
    <?php if ($navRole == 'Admin') { ?>
        <div class="showback">
            <h3>Admin Area</h3>
            <h4>
                Pending Notifications: <?=$unsent_notification_count?> <?php $this->Form->resetTemplates(); echo $this->Form->postLink(__('Clear Queue'), ['action' => 'clearQueue'], ['confirm' => __('Are you sure you want to mark {0} notifications as "Cleared from Queue"?', $unsent_notification_count)]) ?>
            </h4>
            <h4>
                Notifications Are:
                <?=$this->FeatureFlags->getStatusBadge("notification_sending_enabled")?>
                <?=$this->FeatureFlags->getToggleLink("notification_sending_enabled")?>
            </h4>
        </div>
    <?php } ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>
                <?=$this->Paginator->sort('id')?>
            </th>
            <th>
                <?=$this->Paginator->sort('created')?>
            </th>
            <th>
                <?=$this->Paginator->sort('status')?>
            </th>
            <th>
                <?=$this->Paginator->sort('notification_level')?>
            </th>
            <th>
                <?=$this->Paginator->sort('source_type')?>
            </th>
            <th>
                <?=$this->Paginator->sort('source_id')?>
            </th>
            <th>
                <?=$this->Paginator->sort('message')?>
            </th>
            <th>
                <?=$this->Paginator->sort('rule_id')?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php $__iterator0 = isset($notifications) ? $notifications : [];?>
        <?php foreach ($__iterator0 as $notification) {?>

            <tr>
                <td>
                    <?=$this->Number->format($notification->id)?>
                </td>
                <td>
                    <?=$this->Time->timeAgoInWords($notification->created)?>
                </td>
                <td>
                    <?=$this->Enum->enumKeyToValue('Notifications','status',$notification->status)?>
                </td>
                <td>
                    <?=$this->Enum->enumKeyToValue('RuleActions','notification_level',$notification->notification_level)?>
                </td>
                <td>
                    <?=$this->Enum->enumKeyToValue('Notifications','source_type',$notification->source_type)?>
                </td>
                <td>
                    <?=$this->Number->format($notification->source_id)?>
                </td>
                <td>
                    <?=isset($notification->message) ? $notification->message : ''?>
                </td>
                <td>
                    <?=$notification->has('rule') ? $this->Html->link($notification->rule->id, ['controller' => 'Rules', 'action' => 'this', $notification->rule->id]) : ''?>
                </td>
            </tr>
        <?php }?>
        <?php unset($__iterator0);?>
        </tbody>
    </table>
    <div id="systemLogNotificationLevelSelect">
        <?=isset($this->Form->start) ? $this->Form->start : ''?>
        <?=$this->Form->input('notification_level', ['value'=>$notification_level, 'empty' => 'All Notifications','options' => $this->Enum->selectValues('RuleActions','notification_level'),'v-on:change'=>"changeLevel"])?>
        <?=isset($this->Form->submit) ? $this->Form->submit : ''?>
    </div>
    <script type="text/javascript">
        var systemLogNotificationLevelSelect = new Vue({
            el: '#systemLogNotificationLevelSelect',
            methods: {
                changeLevel: function () {window.location.pathname = '/notifications/index/' + $(this.$el).find('select#notification-level').val();
                }
            }
        });
    </script>
</div>
<?=$this->element('paginator')?>