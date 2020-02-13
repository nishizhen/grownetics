# Building

Run `docker build .` to build the docker image.

# Running

Run `docker run -v $(pwd)/:/home/ -it python` to run the script.

# How it works

This script takes in an `input.csv` file and outputs an `output.csv` file.

At the top of the script, `rooms` and `median_ww` is defined.

These need to be populated with a list of zone_ids, as well as median wet weights of the plants from those zone IDs.

The `input.csv` file should contain historical environmental data for the zone IDs in question,
for the time period when the plants corresponding to the median wet weights, were in those zones.

# Generating Input CSV

To get the input CSV, go to the Chronograf of the facility in question, and enter this query:
`SELECT mean("value") AS "mean_value" FROM "sensor_data"."autogen"."sensor_data" WHERE time > :dashboardTime: AND ("type"='2' OR "type"='3' OR "type"='4') AND "source_type"='1' GROUP BY time(:interval:), "type", "source_id" FILL(null)`

Adjust the timeframe to be the time required above, and that's it.