#!/bin/bash

curl -s -H "Content-Type: application/json" \
    -XPOST http://admin:admin@localhost:3000/api/datasources \
    -d @- <<EOF
{
    "name": "influx",
    "type": "influxdb",
    "access": "proxy",
    "url": "http://influxdb:8086",
    "database": "sensor_data"
}
EOF

pkill grafana-server
timeout 10 bash -c "while </dev/tcp/localhost/3000; do sleep 1; done"

exec ./run.sh "${@}"