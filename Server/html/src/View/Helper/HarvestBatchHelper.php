<?php

namespace app\View\Helper;

use Cake\View\Helper;

class HarvestBatchHelper extends Helper {
    public function getPercentDone($batch,$precision=0) {
        $totalGrowTime = strtotime($batch['HarvestBatch']['harvest_date'])-strtotime($batch['HarvestBatch']['planted_date']);
		$currentGrowTime = strtotime($batch['HarvestBatch']['harvest_date'])-time();
        if ($currentGrowTime && $totalGrowTime) {
    		$result = round(100-($currentGrowTime/$totalGrowTime*100),$precision);
    		$percent = ($result < 100) ? $result : 100;
    		$percent = ($percent > 0) ? $percent : 0;
        } else {
            $percent = 0;
        }
		return $percent;
    }
    public function enumValueToKey($field,$value) {
    	$HarvestBatch=ClassRegistry::getObject('HarvestBatch'); 
    	return $HarvestBatch->enumValueToKey($field,$value);
    }
}