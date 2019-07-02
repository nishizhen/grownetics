# Data

## Start Jupyter

```
cd DataScience/
docker-compose up
```

# Useful Chronograf Queries

Get humidity and temp for every zone at ACS: `SELECT mean("value") AS "mean_value" FROM "sensor_data"."autogen"."sensor_data" WHERE time > :dashboardTime: AND ("source_id"='15' OR "source_id"='16' OR "source_id"='17' OR "source_id"='18' OR "source_id"='19' OR "source_id"='20' OR "source_id"='21' OR "source_id"='22' OR "source_id"='23' OR "source_id"='24' OR "source_id"='25' OR "source_id"='26' OR "source_id"='27' OR "source_id"='28' OR "source_id"='29' OR "source_id"='30' OR "source_id"='31' OR "source_id"='32') AND "source_type"='1' AND ("type"='2' OR "type"='3') GROUP BY time(1h), "source_id", "type" FILL(null)`