<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;
use Twilio\Rest\Client;
use App\Lib\DataConverter;
use App\Lib\SystemEventRecorder;
use Cake\Cache\Cache;

/**
 * Notifications Model
 *
 * @property \App\Model\Table\RulesTable|\Cake\ORM\Association\BelongsTo $Rules
 *
 * @method \App\Model\Entity\Notification get($primaryKey, $options = [])
 * @method \App\Model\Entity\Notification newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Notification[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Notification|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Notification patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Notification[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Notification findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @mixin \App\Model\Behavior\EnumBehavior
 */
class NotificationsTable extends Table
{
    use SoftDeleteTrait;

    public $enums = array(
        'status' => array(
            0 => 'Queued',
            1 => 'Sent',
            2 => 'Confirmed',
            3 => 'Cleared',
        ),
        'source_type' => array(
            'Sensor',
            'Zone',
            'HarvestBatch',
            'Device',
            'System',
            'Admin',
        )
    );

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('notifications');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Enum');
        $this->addBehavior('FeatureFlags.FeatureFlags');

        $this->belongsTo('Rules', [
            'foreignKey' => 'rule_id'
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);

        $this->addBehavior('Muffin/Footprint.Footprint', [
            'events' => [
                'Model.beforeSave' => [
                    'show_metric' => 'always'
                ]
            ],
            'propertiesMap' => [
                'show_metric' => '_footprint.show_metric',
            ],
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->integer('notification_level')
            ->requirePresence('notification_level', 'create')
            ->notEmpty('notification_level');

        $validator
            ->integer('source_type')
            ->allowEmpty('source_type');

        $validator
            ->boolean('deleted')
            ->allowEmpty('deleted');

        $validator
            ->dateTime('deleted_date')
            ->allowEmpty('deleted_date');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['rule_id'], 'Rules'));

        return $rules;
    }

    public function formatMessage($rule_condition) {
        $this->Users = TableRegistry::get('Users');
        $this->DataPoints = TableRegistry::get('DataPoints');
        $this->Sensors = TableRegistry::get('Sensors');
        $this->RuleConditions = TableRegistry::get('RuleConditions');
        $this->Zones = TableRegistry::get('Zones');

        $sensor_type = $this->Sensors->enumKeyToValue('sensor_type',$rule_condition['sensor_type']);
        $rule_threshold_type = ($rule_condition['operator'] == 1) ? "fallen below" : "exceeded";

        # This is set in RuleConditionsTable->processRules.
        # We save this in cache earlier rather than calculating it now because it may have changed between now and then
        $actual_value = Cache::read('last-trigger-value-for-rule-condition-'.$rule_condition->id);

        if ($rule_condition['sensor_type']) {
            $converter = new DataConverter();
            # TODO: Change the last parameter (metric: false) to actually use correct metric settings based on the user being notified.
            # This can't be done until notifications are sent out individually per user rather than one notification system-wide.
            if ($this->RuleConditions->enumKeyToValue('data_source', $rule_condition['data_source']) == 'Zone') {
                $zone = $this->Zones->get($rule_condition['data_id']);
                $rule_data_source = $zone->label;
            } else {
                $rule_data_source = $rule_condition['data_id'];
            }
            $converted_threshold_value = $converter->displayUnits($rule_condition['trigger_threshold'], $this->Sensors->enumKeyToValue('sensor_data_type', $rule_condition['sensor_type']), false);
            $converted_actual_value = $converter->displayUnits($actual_value, $this->Sensors->enumKeyToValue('sensor_data_type', $rule_condition['sensor_type']), false);
            $converted_symbol = $this->Sensors->enumKeyToValue('sensor_symbols', $rule_condition['sensor_type']);

            return $sensor_type . " reading from " . $this->RuleConditions->enumKeyToValue('data_source', $rule_condition['data_source']) . " " . $rule_data_source . " has " . $rule_threshold_type . " the rule threshold of " . $converted_threshold_value . $converted_symbol . ", it's current value is " . $converted_actual_value . $converted_symbol . "";
        } else {
            return $rule_condition['label'];
        }
    }

    public function actOnRule($ruleAction, $notificationRuleCondition) {
        $this->Rules = TableRegistry::get('Rules');
        $this->RuleConditions = TableRegistry::get('RuleConditions');
        $rule = $this->Rules->get($ruleAction->rule_id);
        $ruleMessage = $this->formatMessage($notificationRuleCondition);
        $message = 'Sent by rule '.$rule->id.': '.$ruleMessage.'.';
        $notification = $this->newEntity(array(
            'source_id'=>   $ruleAction->id,
            'status' => $this->enumValueToKey('status','Queued'),
            'message' => $message,
            'notification_level' => $ruleAction->notification_level,
            'rule_id' => $ruleAction->rule_id
        ));
        $this->save($notification);
    }

    public function clearQueue() {
        $this->updateAll(
            [  // fields
                'status' => $this->enumValueToKey('status','Cleared')
            ],
            [  // conditions
                'status' => 0
            ]
        );
    }

    public function process($shell) {
        # Find out if notifications have been enabled
        $notifications_enabled = $this->getFeatureFlagValue("notification_sending_enabled");
        if (!$notifications_enabled) {
            $shell->out("Notifications are not enabled! Skipping.");
            return false;
        }
        $shell->out("Processing notifications..");
        $this->UserContactMethods = TableRegistry::get('UserContactMethods');
        $this->Rules = TableRegistry::get('Rules');
        $this->RuleActions = TableRegistry::get('RuleActions');

        $params = array(
            'conditions' => array('status' => $this->enumValueToKey('status','Queued')),
            'limit' => 10
        );
        $notifications = $this->find('all', $params);

        $twilio_sid = env('TWILIO_SID');
        $twilio_token = env('TWILIO_TOKEN');

        $recorder = new SystemEventRecorder();
        $recorder->recordEvent('system_events', 'notifications_in_queue', $notifications->count());
        $recorder->recordEvent('system_events', 'notifications_processed', count($notifications->toArray()));

        foreach ($notifications as $notification) {
            $shell->log($notification, 'debug');

            // Grownetics outgoing number.
            $outgoing_number = '4696434769';

            if ($notification->notification_level > 0 && !env('DEV')) {
                $command = 'curl https://grownetics.zulipchat.com/api/v1/messages -u growbot-bot@grownetics.zulipchat.com:kYKZ4EDs7gcbIff55ud7aMKM3x86QFR8 -d "type=stream" -d "to=alerts" -d "subject=' . env('FACILITY_NAME') . '" -d "content=** Notification Level: ' . $this->RuleActions->enumKeyToValue('notification_level', $notification->notification_level) . ' - *Alert:* ' . $notification['message'] . '"';
                exec($command);
                echo $command;
            }

            switch ($notification->notification_level) {
                case $this->RuleActions->enumValueToKey('notification_level', 'Email'):
                    echo "Email";
                    $contact_emails = $this->UserContactMethods->find('all', ['conditions' => [
//                        'user_id' => $notification->user_id,
                        'type' => $this->UserContactMethods->enumValueToKey('type', 'Email')
                    ]]);
                    foreach ($contact_emails as $contact_email) {
                        echo "Send to " . $contact_email['value'];
                        Email::deliver($contact_email['value'], 'Grownetics Alert!', $notification->message, ['from' => 'no-reply@grownetics.co']);
                    }

                    break;
                case $this->RuleActions->enumValueToKey('notification_level', 'Text Message'):
                    echo "Try and send";
                    // Instantiate a new Twilio Rest Client
                    $contact_methods = $this->UserContactMethods->find('all', ['conditions' => [
//                        'user_id' => $notification->user_id,
                        'type' => $this->UserContactMethods->enumValueToKey('type', 'SMS Number')
                    ]]);
                    try {
                        $client = new Client($twilio_sid, $twilio_token);

                        foreach ($contact_methods as $contact_method) {
                            if (strlen($contact_method['value']) > 1) {
                                $client->account->messages->create(

                                    $contact_method['value'],

                                    array(
                                        'from' => $outgoing_number,
                                        'body' => env('FACILITY_NAME') . ' Facility Alert. ' . $notification->message
                                    )
                                );

                                echo "Sent message to " . $contact_method['value'];
                            }
                        }
                        $recorder->recordEvent('system_events', 'notification_send_success', 1, [
                            'notification_level' => $notification->notification_level
                        ]);
                    }
                    catch (\Exception $e) {
                        $shell->log($e);
                        $shell->log("Could not send to twilio");
                        $recorder->recordEvent('system_events', 'notification_send_failure', 1, [
                            'notification_level' => $notification->notification_level
                        ]);
                    }
                    break;
                case $this->RuleActions->enumValueToKey('notification_level', 'Phone Call'):

                    $contact_methods = $this->UserContactMethods->find('all', ['conditions' => [
//                        'user_id' => $notification->user_id,
                        'type' => $this->UserContactMethods->enumValueToKey('type', 'Phone Number')
                    ]]);
                    $client = new Client($twilio_sid, $twilio_token);
                    foreach ($contact_methods as $contact_method) {
                        $client->calls->create(
                            $contact_method['value'],
                            $outgoing_number,
                            array(
                                'url' => 'http://' . env('REMOTE_URL') . '/notifications/out/' . $notification->id
                            )
                        );
                    }

                    break;
                case $this->RuleActions->enumValueToKey('notification_level', 'Facility Alert'):
                    // TODO:

                    break;
                case $this->RuleActions->enumValueToKey('notification_level', 'Fire Alarm'):
                    // TODO:

                    break;
            }
            $notification->status = $this->enumValueToKey('status','Sent');
            $this->save($notification);
        }
    }
}
