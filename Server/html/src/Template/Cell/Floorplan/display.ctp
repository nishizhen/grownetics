<?php
/**
 * @var \App\Model\Entity\Floorplan $floorplan
 */
?>
<div style="display:none">
<script>

        var GrowServer = GrowServer || {};
        GrowServer.Floorplan = GrowServer.Floorplan || {};

        GrowServer.Floorplan.id = '<?= $floorplan->id ?>';

        GrowServer.Floorplan.center = ['<?=$floorplan->latitude?>','<?=$floorplan->longitude?>'];

        GrowServer.Floorplan.map_item_types = '<?=json_encode($mapItemTypes)?>';

        //GrowServer.Floorplan.layers = {
        //    sensors:'<?//=json_encode($sensorData)?>//',
        //    map_items:'<?//=json_encode($floorplan->map_items)?>//',
        //    plant_data:'<?//=json_encode($plantsData)?>//',
        //    zones:'<?//=json_encode($floorplan->zones)?>//',
        //    background_image: '/img/uploads/'+'<?//=$floorplan->floorplan_image?>//',
        //    floorplan_geoJSON:'<?//=$floorplan->geoJSON?>//'
        //}
        //
        //GrowServer.sensorData = '<?//= json_encode($sensorData)?>//';

</script>

<style>
    #leaflet-map { min-width: 768px; min-height: 432px; }
    #leaflet-map.leaflet-container { background-color: transparent; }
</style>
</div>
<div id="leaflet-map"></div>
<div id="leaflet-layer-control">
    <ul>
        <li></li>
    </ul>
</div>