<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class EnumHelper extends Helper
{

    public function enumValueToKey($table,$field,$value) {
    	$table = TableRegistry::get($table);
        return $table->enumValueToKey($field,$value);
    }
    public function enumKeyToValue($table,$field,$key) {
        $table = TableRegistry::get($table);
        return $table->enumKeyToValue($field,$key);
    }
    public function selectValues($table,$field) {
        $table = TableRegistry::get($table);
        return $table->enums[$field];
    }
}