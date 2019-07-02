curl -XPOST -u admin:admin -i http://localhost:3000/api/datasources -H "Content-Type: application/json"     --data-binary <<DATASOURCE \
      '{
        "name":"Sensor Data",
        "type":"influxdb",
        "url":"http://influxdb:8086",
        "access":"proxy",
        "isDefault":true,
        "database":"sensor_data"
      }'
DATASOURCE

curl -XPOST -u admin:admin -i http://localhost:3000/api/datasources -H "Content-Type: application/json"     --data-binary <<DATASOURCE \
      '{
        "name":"Telegraf",
        "type":"influxdb",
        "url":"http://influxdb:8086",
        "access":"proxy",
        "isDefault":false,
        "database":"telegraf"
      }'
DATASOURCE

curl -XPOST -u admin:admin -i http://localhost:3000/api/datasources -H "Content-Type: application/json"     --data-binary <<DATASOURCE \
      '{
        "name":"System Events",
        "type":"influxdb",
        "url":"http://influxdb:8086",
        "access":"proxy",
        "isDefault":false,
        "database":"system_events"
      }'
DATASOURCE

curl -XPOST -u admin:admin -i http://localhost:3000/api/datasources -H "Content-Type: application/json"     --data-binary <<DATASOURCE \
      '{
        "name":"Faker Data",
        "type":"influxdb",
        "url":"http://influxdb:8086",
        "access":"proxy",
        "isDefault":false,
        "database":"faker_data"
      }'
DATASOURCE

for dash in $(ls /grafana/dashboards); do
    curl -XPOST -u admin:admin -i http://localhost:3000/api/dashboards/db --data-binary @/grafana/dashboards/$dash -H "Content-Type: application/json"
done