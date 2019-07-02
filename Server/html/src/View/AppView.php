<?php
namespace App\View;
use JadeView\View\JadeView;

/**
 * @property \App\View\Helper\EnumHelper $Enum
 * @property \App\View\Helper\HarvestBatchHelper $HarvestBatch
 * @property \App\View\Helper\FeatureFlagHelper $FeatureFlag
 * @property \AssetCompress\View\Helper\AssetCompressHelper $AssetCompress
 * @property \App\View\Helper\ConverterHelper $Converter
 * @property \App\View\Helper\DiffHelper $Diff
 * @property \App\View\Helper\GrowFakerHelper $GrowFaker
 */
class AppView extends JadeView
{
    public function initialize()
    {
        $this->viewOptions([
            'pretty' => true
        ]);

        parent::initialize();


        $this->loadHelper('Form', [
        	'errorClass' => 'has-error',
			'templates' => 'app_form',
		]);
		$this->loadHelper('Paginator', [
			'templates' => 'paginator_templates'
		]);
        $this->loadHelper('Html');
        $this->loadHelper('Flash');
        $this->loadHelper('Enum');
    }
}