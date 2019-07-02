<?php
use Migrations\AbstractMigration;
use Cake\ORM\TableRegistry;

class AddZoneSetPoints extends AbstractMigration
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
        $this->SetPoints = TableRegistry::get('SetPoints');
        $setPoints = $this->fetchAll('SELECT * FROM set_points WHERE target_type ='.$this->SetPoints->enumValuetoKey('target_type', 'Zone Type'));
        foreach ($setPoints as $setPoint) {
            if ($setPoint['status'] != $this->SetPoints->enumValuetoKey('status', 'Enabled')) {
                $setPoint['status'] = $this->SetPoints->enumValuetoKey('status', 'Enabled');
                $this->execute("UPDATE set_points SET status = ". $this->SetPoints->enumValuetoKey('status', 'Enabled')." WHERE id = ".$setPoint['id']);
            }
        }
        $this->SetPoints->generateFromDefaultSetPoints();
    }
}
