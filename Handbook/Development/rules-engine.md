# Rules Engine

Rules can have multiple Rule Conditions, and multiple Rule Actions. If
it has mulitple Rule Conditions, they must all be true to trigger the
rule (for now). If it has multiple Rule Actions they will all be
triggered when the rule is triggered. Each Rule Action can have multiple
Rule Action Targets. A Rule Action Target points at an Output, Set
Point, Appliance, Appliance Template, or Appliance Type.

## Rule Types

Rules can have one of three types.

### Regular

Just standard functionality.

### Default

Attaches to a plant zone type. Doesn't actually control anything. Ghost Rules do that for Default rules. 

### Ghost

Stores status and pending time for Default Rules for a particular zone.
Looks up its thresholds and delay time from the Default rule it points to.

## Set Points

Set Points are placeholders for certain values that we output.

If a Rule Action Target has `target_type='Set Point'` then  we look at
it's Set Point object.

## Appliances

An Appliance is the most specific of the three appliance tables. This
refers to a specific light.

An Appliance Temeplate is the next level up, it specifies rules that
apply to all Heliospectra 819x lights for example.

An Appliance Type is the most general, it specifies rules that apply to
all types of grow lights in the facility.

## Sensors

### sensor_type

This refers to a general type of sensor. Like 'Waterproof Temperature
Sensor' or 'Air Temperature Sensor'.

### data_type

This refers to the general type of data returned by the sensor. Both
the sensors above would have 'Temperature' as their data_type.

## Default Rules

Default rules let us do things like set all Zones of a certain Plant
Zone Type to the same baseline conditions. They are overridden by either
Rules created pointing at a specific zone, or by recipes which are
curently in a given zone.

With each new default zone rule, whenever a zone of that zone type
generates data, it should create a new Rule, RuleConditions,
RuleActions, and RuleActionTargets for that zone.

### Rules
- `status`: Disabled, Enabled, Triggered
- `autoreset`: boolean; whether or not the rule should revert back to Enabled after being Triggered.
- `is_default`: boolean; use Zone's plant_zone_type_id default rule or Zone specific rule.

### Rule Conditions
Rule Conditions say things like X data point needs to be above Y value for Z time. Currently, all conditions on a Rule need to be true to trigger the Rule. In the future it shouldn't be hard to add 'OR' logic.
- `data_source`: Data Point, Zone, Time, Interval, Zone Type, Zone Type Target; Source of the input data to compare with Rule Condition thresholds.
        - `Data Point`: single sensor
        - `Zone`: zone average
        - `Time`: time in seconds
        - `Interval`: not currently supported
        - `Zone Type`: used with default set points, to pass generic zone set point fields to zone specific set points. 
        - `Zone Type Target`: not currently used
- `data_type`: Type of data the rule will be acting on (Air temp, humidity, Co2, etc).
- `data_id`: Either a Sensor ID or Zone ID (based on data_source), specifiying where the data is coming from.
- `operator`: greater than > or less than <; how we compare the input data to the threshold.
- `trigger_threshold`: Value at which the rule should be Triggered depending on the operator. E.g. 1150 and operator is < would mean, turn on Co2 when input data is < 1150.
- `reset_threshold`: Value at which the rule will be set to Enabled. E.g. value 1200 and operator is < would mean, turn off Co2 when input data is > 1200. 
- `status`: Disabled, Enabled, Triggered.
- `zone_behavior`: Single Sensor, Average of Sensors.
       - Single Sensor: **The data_id must be a Sensor ID** when set to Single Sensor. Will look at the input data from that 1 sensor.
       - Average of Sensors: **The data_id must be a Zone ID** when set. Will look at the input data from the average of all the Sensors within the Zone.
- `trigger_delay`: Delay the time (in seconds) of the Rule being set to Triggered. 
- `pending_time`: Timestamp for last time rule was triggered, used to make sure trigger_delay works.
- `rule_id`: ID of the Rule linked to this Rule Condition.
- `is_default`: Look at the zone's plant_zone_type_id Rule Condition or zone specific.
- `default_condition_id`: ID of the Zone's plant_zone_type_id rule condition. Only set when the Zone does not have a specific Rule Condition.

## Rule Actions
Rule Actions can be like 'Turn On' X, 'Turn Off' Y, 'Send Notification' of level Z to user role ∑. Can be multiple things per rule. Turn off grow lights, turn on emergency cooling, send alarm, all triggered from one Rule Condition.
- `type`: Notification Only, Sensor Update, Turn On, Turn Off, Toggle, Set Point.
        - `Notification Only`: Send a notification.
        - `Sensor Update`: Not currently supported.
        - `Turn On`: Change an Output's status from Enabled to Powered.
        - `Turn Off`: Change an Output's status from Powered to Enabled.
        - `Toggle`: Not currently used.
        - `Set Point`: Set the Set Point's status to Set.
- `notification_level`: Logged Only, Dashboard Notification, Dashboard Alert, Dashboard Alarm, Email, Text Message, Phone Call.
        - `Logged Only`: Logged into InfluxDB.
        - `Dashboard Notification`: Add an entry in the Notifications table and will display in Notification views (header dropdown, notification box).
        - `Dashboard Alert`: Level 1 dashboard alert.
        - `Dashboard Alarm`: Level 2 dashboard alert.
        - `Email`: Will send an email to users.
        - `Text Message`: Send a text message to users.
        - `Phone Call`: Call users with an automated message.
- `notification_user_role`: Send a notification to all users with this role_id. E.g. send a notification to only and all 'Growers'.
- `rule_id`: ID of the rule linked to this Rule Action.
- `on_trigger`: boolean; 
  If true (default), perform the action when the rule is triggered.
  If false, perform it when it is reset. This can be good to send notifications when things return to normal. 
- `is_default`: Look at the Zone's plant_zone_type_id generic rule action (true), or point to a Zone's specific rule action (false).

## Rule Action Targets
- `rule_action_id`: ID of the Rule Action (note not Rule) linked to this Rule Action Target. 
- `target_type`: Output, Set Point, Appliance, ApplianceTemplate, ApplianceType.
        Let's us act on different types of hardware. 
- `target_id`: ID of the Output / Set Point / Appliance that the Rule is acting on. 
- `status`: Disabled, Enabled, Powered, Set.
- `output_value`:
- `output_object`: 
- `output_property`:
- `is_default`: Look at the Zone's plant_zone_type_id generic rule
action target (true), or point to a Zone's specific rule action target
(false).

## Appliance Templates

Right now this is just a label. Something like '<i>315 120v Light</i>'.
Eventually (soon) we'll add `appliance_type_id` which specifies what
type of template this template is

## Appliance Types
 
Which will have more general 'Light' that we can link to map icons,
display styles, etc.

# Outputs

Outputs are listed in the system currently as `Hardware` to be more
customer friendly.

In reality they correspond to a physical pin on a physical device at a
facility, or to an API endpoint that represents a physical device.

## Hardware Types

An Output has a `hardware_type` field. This field is used to convey
that certain outputs are lights, and our lighting devices should
keep them turned on according to a predetermined schedule even after
the connection to the server goes away. This is in contrast to other
`hardware_type`s such as Co2 Doser which turn off as soon as the server
connection goes away.

# Timed Rules

Timed rules have a `data_source` of `3` on their `rule_conditions`.  `operators` 
are `null` or `0` and use a `trigger_threshold` and `reset_threshold` to fire and
reset `rule_actions` respectively.  These two columns values' are represented in seconds past
UTC midnight. For example if a `rule_condition` has a `trigger_threshold` of `3600` 
and a `data_source` of `3`, the `rule_action` will trigger 60 minutes past UTC midnight
ie. 7PM MST.
