<div class="form-group col-sm-4" style="padding-left: 0px !important;">
    <?php if (isset($setPoint)): ?>
    <input style="<?php if ($setPoint->target_type == 1 && $this->request->params['controller'] == 'Zones'): ?> border:1px solid rgba(26,140,255,0.5); color: rgba(0, 0, 0, 0.5); <?php else: ?> border:1px solid rgb(26,140,255); font-weight: bold; <?php endif; ?>" type="number" class="form-control setPointInput"
           value="<?php if ($setPoint) { echo $setPoint->value; } ?>">
    <button style="display:none; font-size: 0.9em;" class="btn btn-xs btn-success fa fa-check SaveSetPoint"
            data-set_point_id="<?php if ($setPoint) { echo $setPoint->id; } ?>"
            data-set_point_type="<?php if ($setPoint) { echo $setPoint->target_type; } ?>"
            data-controller="<?=$this->request->params['controller']?>"
            data-data_type="<?=$parSensorType?>"
    ></button>
    <button style="display:none; font-size: 0.9em;" class="btn btn-xs btn-danger fa fa-times RevertSetPoint"></button>
</div>
<button style="font-size: 0.7em; <?php if ($setPoint->target_type == 1): ?>  display:none; <?php endif; ?>" class="btn btn-xs btn-theme ResetToDefault fa fa-refresh"></button>
<?php endif; ?>
<i style="visibility: hidden" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i>