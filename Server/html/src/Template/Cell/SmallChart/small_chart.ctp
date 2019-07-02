<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="col-md-6 mb small_chart">
            <div class="green-panel content-panel small_chart_content">
              <div class="green-header">
                <div class="row">
                  <div class="col-sm-10 goleft">
                    <h5 class="chart-title" data-chartid="<?=$count?>" data-sourcetype="<?=$chart['source_type']?>" data-sourceid="<?=$chart['source_id']?>" data-sourcelabel="<?=$chart['source_label']?> " data-datatype="<?=$chart['sensor_type_label']?>" data-sensortypesymbol="<?=$chart['sensor_type_symbol']?>">
                    Select an input...</h5>
                  </div>
                  <div style="float:left">
                    <button style="visibility:hidden;" class="btn btn-primary editChartBtn" type="button" data-toggle="collapse" data-target="#<?=$count?>">Edit
                    </button>
                  </div>
                  <div class="settingsMenu">
                   <div class="collapse" id="<?=$count?>" style="width:100%;">
                    <div class="well" style="margin: 0; text-align: center !important;">
                      Graph <?=$count?> -
                      <button class = "btn" data-toggle="modal" data-target="#myModal<?=$count?>"><span class="fa fa-trash"></span>
            </button>

                      <div class="modal fade" id="myModal<?=$count?>" tabindex="-1" role="dialog" style="display: none;">
                 <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Confirm</h4>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to delete Graph - <?=$count?>?
                  </div>
                  <div class="modal-footer">

                    <button  class="btn btn-success btn-sm deleteChart" dataId="<?=$count?>" data-dismiss="modal">Yes</button>
                    <button  class="btn btn-sm" data-dismiss="modal">No</button>
                  </div>
                </div>
                </div>
              </div><div>
<?php echo $this->cell('DataSelector::display_small'); ?></div>
                          <div class="form-group thresholdInput">
                              <label class="control-label col-md-3">Threshold Range:</label>
                              <div class="col-md-9">
                                  <div class="input-group input-large">
                                      <input type="text" class="form-control dpd1" id="lowThresholdInput" name="from"></input>
                                      <span class="input-group-addon">To</span>
                                      <input type="text" class="form-control dpd2" id="highThresholdInput" name="to"></input>

                                  </div>                                                     
                                  <span class="help-block"><span style="display:initial" id="thresholdHelpText">LOW : HIGH </span><button style="display:none;" class="btn btn-xs btn-success submitEditChartBtn" type="submit"> Save
                    </button><span style='color: red; display:none'> * Not a number. </span></span>

                              </div>
                          </div>
                </div>
              </div>

            </div>

          </div>

        </div>
        <div style="width:100%; height:300px;" id="chartdiv<?=$count?>"></div>
        <i style = "position:relative; bottom:200px;" class="spinner fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>

        <canvas></canvas>
      </div>
    </div>