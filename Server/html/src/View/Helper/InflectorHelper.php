<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Utility\Inflector;

class InflectorHelper extends Helper
{

    public function humanize($value) {
    	return Inflector::humanize($value);
    }
}