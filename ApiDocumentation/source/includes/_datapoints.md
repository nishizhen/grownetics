# Data Points

A Data Point in the Grownetics system is one individual sensor reading at one point in time.

Any Data Point written to the system must have `value`, `timestamp` and `sensor_id` values set.

### Value

All values should be transmitted in Metric values. Celcius for temperature, grams and kilograms for weight, etc.

### Timestamp

A UNIX Epoch Timestamp value from when the sensor reading was taken. Example: `1529601200`

### Sensor ID

Sensor ID corresponds to a [Sensor](#sensors) that you have previously created.

## Write Data Points

```shell
curl "https://api.grownetics.co/v1/datapoints/add"
  --data "[
  {
    "sensor_id"=>8,
    "value"=>45.6,
    "timestamp"=>1529601200,
  }
  ]"
  -H "Authorization: growgrowgrow"
```

> The above command returns JSON structured like this:

```json
{
  "success":"true"
}
```

### HTTP Request

`POST https://api.grownetics.co/v1/datapoints/add`

### Data Point Parameters

Parameter | Description
--------- | -----------
sensor_id | Must be a valid ID of a [Sensor](#sensors) created previously.
value | Must be a numeric metric value in the unit listed for the corresponding [data_type](#data-types).