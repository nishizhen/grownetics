<?php
use Migrations\AbstractMigration;

class AddRowsToSensorsZones extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $rows = $this->fetchAll('SELECT * FROM sensors');
        $zones = $this->fetchAll('SELECT * FROM zones');
        $data = [];
        $zoneIds = [];
        foreach ($zones as $zone) {
            $zoneIds[] = $zone['id'];
        }
        foreach($rows as $row) {
            if (in_array($row['zone_id'], $zoneIds)) {
                $data[] = ['sensor_id' => $row['id'], 'zone_id' => $row['zone_id']];   
            }
        }
        if ($data != NULL) {
            $this->insert('sensors_zones', $data);
        }
        
    }
}
