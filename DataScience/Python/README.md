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
