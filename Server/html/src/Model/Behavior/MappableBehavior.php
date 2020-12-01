<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;


/**
 * Mappable behavior
 */
class MappableBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function initialize(array $config)
    {
        // Some initialization code here
        if (get_class($this->_table) != "App\Model\Table\MapItemsTable") {
            $this->_table->belongsTo('MapItems');
        }
    }

    public function beforeMarshal($event, $data) {
        if (isset($data['zone_id']) && is_string($data['zone_id'])) {
            $Zones = TableRegistry::get("Zones");
            $zoneEntity = $Zones->find()->where(['label' => $data['zone_id']])->first();
            if (isset($zoneEntity)) {
                $data['zone_id'] = $zoneEntity->id;
            }
        }
    }

    public function afterSaveCommit($event, $entity)
    {
        if ($entity->map_item_id && !$entity->isNew() && $entity->label) {
            $MapItems = TableRegistry::get('MapItems');
            $map_item = $MapItems->get($entity->map_item_id);
            $map_item->label = $entity->label;
            $MapItems->save($map_item);
        }
    }

    public function beforeSave($event, $entity)
    {
        if ($entity->isNew() && $entity->dontMap == false) {

            $klass = (new \ReflectionClass($entity))->getShortName();

            $this->MapItems = TableRegistry::get('MapItems');
            $this->Zones = TableRegistry::get("Zones");
            $this->MapItemTypes = TableRegistry::get("MapItemTypes");

            $zoneEntities = [];

            if (isset($entity->zones)) {
                foreach ($entity->zones as $zone) {
                    if (is_string($zone)) {
                      $zoneEntity = $this->Zones->find()->where(['label' => preg_replace('/[\-|_]/',' ',$zone)])->first();
                        if (isset($zoneEntity)) {
                            array_push($zoneEntities, $zoneEntity);
                        }
                    } else { // hope it's a zone object
                        array_push($zoneEntities, $zone);
                    }
                }
                $entity->zones = $zoneEntities;
                $entity->dirty('zones', true);
            } else if (is_string($entity->zone_id)) {
                $zoneLabel = $entity->zone_id;
                $zoneEntity = $this->Zones->find()->where(['label' => $zoneLabel])->first();
                array_push($zoneEntities, $zoneEntity);
            }

            $mapEntity = $this->MapItems->newEntity([
                'label' => $entity->label
            ]);

            $mapEntity->floorplan_id = $entity->floorplan_id;
            $mapEntity->type = $klass;

            if (isset($entity->latitude) && isset($entity->longitude)) {
                $mapEntity->latitude = $entity->latitude;
                $mapEntity->longitude = $entity->longitude;
            }

            if (isset($entity->offsetHeight)) {
                $mapEntity->offsetHeight = $entity->offsetHeight;
            }

            if (isset($entity->geoJSON)) {
                $mapEntity->geoJSON = $entity->geoJSON;
            }

            if (isset($zoneEntities)) {
                $mapEntity->zones = $zoneEntities;
            }

            if (!$this->MapItems->save($mapEntity)) {
                // Log::write("debug", "Mappable is not behaving: ");
//                Log::write("debug", $mapEntity->errors());
            } else {
                $entity->map_item = $mapEntity;
            }
        }
    }
}
