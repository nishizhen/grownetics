# Sensors

Each individual sensor stream that you want to save [Data Points](#data-points) for, you must first create a Sensor within the Grownetics system. You will then use the `id` returned from the Sensor to submit along with your Data Points.

## Data Types

Data Type ID | Data Type
------------ | --------
1 | Water Proof Temperature Sensor
2 | Humidity Sensor
3 | Air Temperature Sensor
4 | Co2 PPM Sensor
5 | pH Probe
6 | Dissolved Oxygen Prove (DO)
7 | Electrical Conductivity Probe (EC - Water Salinity)
8 | Current Transformer Sensor (CT - Power)
9 | Reservoir Fill Level Sensor
11 | Photosynthetic Active Radiation Sensor (PAR - Light)
13 | Soil Moisture Probe

## Create a Sensor

In order to [Write Data Points](#write-data-points) to the Grownetics system, you first must create a Sensor for every data source and type you wish to integrate. What is meant by data source AND type, is if one physical sensor records two different types of data, say a comibined Humidity and Temperature sensor, you still must create two separate Sensors in the Grownetics system, one for each [Data Type](#data-types).

You must use this `sensor_id` value in future requests to write Data Points.

```shell
curl "https://api.grownetics.co/v1/sensors/add"
  --data "[
  {
    "data_type"=>5,
    "label"=>"Tank 1 pH Probe",
  }
  ]"
  -H "Authorization: growgrowgrow"
```

> The above command returns JSON structured like this:

```json
{
  "sensor_id":"134"
}
```