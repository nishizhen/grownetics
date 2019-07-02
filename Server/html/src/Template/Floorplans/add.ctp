<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Floorplan $floorplan
 */
echo $this->element('leaflet');
$this->Html->script('floorplans/add', ['block' => 'scriptBottom']);
?>

<!-- D3.js include -->
<script src="https://d3js.org/d3.v3.min.js" charset="utf-8"></script>

<div class="floorplans form columns content">
    <?= $this->Form->create($floorplan, ['type' => 'file', 'templateVars' => ['header' => 'Add Floorplan']]) ?>
    <?php
    echo $this->Form->input('label', ['default' => 'Main Floor']);
    echo $this->Form->input('description');
    echo $this->Form->input('floor_level', ['default' => 1]);
    ?>

    <div class="row">
        <div class="col-sm-2">
            <label>Bounding Box</label>
            <div>
                (Align the bounding box as close as possible to the actual location of the floorplan using the tools on the map.)
            </div>
        </div>
        <div class="col-sm-10">
            <div id="facility-map" style="min-width:640px;width:100%;height:360px"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
            Floorplan Image (SVG)
        </div>
        <div class="col-sm-10">           
            <?= $this->Form->file('floorplan_image', array('accept' => '.svg,.jpg,.png')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
            &nbsp;
        </div>
        <div class="col-sm-10">  
           <?= $this->Form->button('Import Floorplan', ['type' => 'import_svg']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
            &nbsp;
        </div>
        <div class="col-sm-10">  
             <?= $this->Form->submit('Save', ['style' => 'display:none;']) ?>
        </div>
    </div>

    <div class="modal fade" id="importingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">SVG Import</h4>
                </div>
                <div class="modal-body">
                    Importing..
                    <i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i>

                    <div class="progress">
                         <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 1%">
                            <span class="sr-only">1% Complete</span>
                          </div>
                    </div>

                    <div id="import-summary">
                    </div>
                    <button type="button" style="display:none" class="btn btn-default" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->

        <fieldset style="display:none;">
            <?php
            echo $this->Form->input('offsetAngle', ['default' => 0]);
            echo $this->Form->input('latitude', ['default' => 0.00000, 'step' => 0.000000000001]);
            echo $this->Form->input('longitude', ['default' => 0.00000, 'step' => 0.000000000001]);

            echo $this->Form->textarea('geoJSON');
            echo $this->Form->textarea('plant_placeholders');
            echo $this->Form->textarea('devices');
            echo $this->Form->textarea('zones');

            echo $this->Form->textarea('plant_placeholders_geoJSON');
            echo $this->Form->textarea('devices_geoJSON');
            echo $this->Form->textarea('zones_geoJSON');

            echo $this->Form->textarea('map_items');
            echo $this->Form->textarea('map_items_geoJSON');


            echo $this->Form->textarea('appliances');
            echo $this->Form->textarea('appliances_geoJSON');
            ?>
        </fieldset>
    <?= $this->Form->end() ?>
</div>
<?php
    $this->Form->resetTemplates();
    echo $this->Form->postLink(__('Import Demo Floorplan'), ['controller' => 'Floorplans', 'action' => 'importDemo']);
?>