#KEY=XXXXXXXXXXXX
HOST="http://localhost:3000"

apt update
apt install -y jq

mkdir -p dashboards && for dash in $(curl -k  $HOST/api/search | jq -r '.[].uri|ltrimstr("db/")'); do
  curl -k  $HOST/api/dashboards/db/$dash  | jq '.dashboard' > dashboards/$dash.json
  # Wrap the json so it can be imported
  echo -e "{\n\"dashboard\":\n$(cat dashboards/$dash.json)" > dashboards/$dash.json
  echo -e ",\n\"overwrite\": false\n}" >> dashboards/$dash.json
  # Strip IDs out
  sed -i '/"id":/c\"id": null,' dashboards/$dash.json
done
