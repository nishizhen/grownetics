<?php
use Migrations\AbstractSeed;

/**
 * Acls seed.
 */
class AclsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */

    # All ACL info is maintained here: https://docs.google.com/spreadsheets/d/1PpH7cpXojQVgYYv0iSVKGa6PlODaxfzZvVaWAAOwNck/edit#gid=0
    public function run()
    {
        $this->execute('TRUNCATE acls');
        $data = [
          [
              'id'      => 1,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'wikis',
              'action'  => 'index',
              'rule' => 'allow'
          ],
          [
              'id'      => 2,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'wikis',
              'action'  => 'view',
              'rule' => 'allow'
          ],
          [
              'id'      => 3,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'wikis',
              'action'  => 'add',
              'rule' => 'allow'
          ],
          [
              'id'      => 4,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'wikis',
              'action'  => 'edit',
              'rule' => 'allow'
          ],
          [
              'id'      => 5,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'wikis',
              'action'  => 'delete',
              'rule' => 'allow'
          ],
          [
              'id'      => 6,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'wikis',
              'action'  => 'history',
              'rule' => 'allow'
          ],
          [
              'id'      => 7,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'wikis',
              'action'  => 'diff',
              'rule' => 'allow'
          ],
          [
              'id'      => 8,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'dash',
              'action'  => '*',
              'rule' => 'allow'
          ],
          [
              'id'      => 9,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'users',
              'action'  => 'account',
              'rule' => 'allow'
          ],
          [
              'id'      => 10,
              'created'       => date(DATE_ATOM),
              'modified'    => date(DATE_ATOM),
              'controller'  => 'users',
              'action'  => 'logout',
              'rule' => 'allow'
          ],
            [
                'id'      => 11,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'index',
                'rule' => 'allow'
            ],
            [
                'id'      => 12,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'view',
                'rule' => 'allow'
            ],
            [
                'id'      => 13,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'markCompleted',
                'rule' => 'allow'
            ],
            [
                'id'      => 14,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'completeNextBatchTask',
                'rule' => 'allow'
            ],
            [
                'id'      => 15,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'markUserTaskCompleted',
                'rule' => 'allow'
            ],
            [
                'id'      => 16,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'addBatchUserTask',
                'rule' => 'allow'
            ],
            [
                'id'      => 17,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'updateBatchTask',
                'rule' => 'allow'
            ],
            [
                'id'      => 18,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'updateBatchUserTask',
                'rule' => 'allow'
            ],
            [
                'id'      => 19,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'deleteBatchProccess',
                'rule' => 'allow'
            ],
            [
                'id'      => 20,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'add',
                'rule' => 'allow'
            ],
            [
                'id'      => 21,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'edit',
                'rule' => 'allow'
            ],
            [
                'id'      => 22,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => 'delete',
                'rule' => 'allow'
            ],
            [
                'id'      => 23,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'harvestbatches',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'      => 24,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'harvestbatches',
                'action'  => 'view',
                'rule' => 'allow'
            ],
            [
                'id'      => 25,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'harvestbatches',
                'action'  => 'add',
                'rule' => 'allow'
            ],
            [
                'id'      => 26,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'harvestbatches',
                'action'  => 'edit',
                'rule' => 'allow'
            ],
            [
                'id'      => 27,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'harvestbatches',
                'action'  => 'delete',
                'rule' => 'allow'
            ],
            [
                'id'      => 28,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'charts',
                'action'  => 'view',
                'rule' => 'allow'
            ],
            [
                'id'      => 29,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'cultivars',
                'action'  => 'index',
                'rule' => 'allow'
            ],
            [
                'id'      => 30,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'cultivars',
                'action'  => 'view',
                'rule' => 'allow'
            ],
            [
                'id'      => 31,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'cultivars',
                'action'  => 'add',
                'rule' => 'allow'
            ],
            [
                'id'      => 32,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'cultivars',
                'action'  => 'edit',
                'rule' => 'allow'
            ],
            [
                'id'      => 33,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'cultivars',
                'action'  => 'delete',
                'rule' => 'allow'
            ],
            [
                'id'      => 34,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'recipes',
                'action'  => 'index',
                'rule' => 'allow'
            ],
            [
                'id'      => 35,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'recipes',
                'action'  => 'view',
                'rule' => 'allow'
            ],
            [
                'id'      => 36,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'recipes',
                'action'  => 'add',
                'rule' => 'allow'
            ],
            [
                'id'      => 37,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'recipes',
                'action'  => 'edit',
                'rule' => 'allow'
            ],
            [
                'id'      => 38,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'recipes',
                'action'  => 'delete',
                'rule' => 'allow'
            ],
            [
                'id'      => 39,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'notifications',
                'action'  => 'index',
                'rule' => 'allow'
            ],
            [
                'id'      => 40,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'outputs',
                'action'  => 'index',
                'rule' => 'allow'
            ],
            [
                'id'      => 41,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'outputs',
                'action'  => 'view',
                'rule' => 'allow'
            ],
            [
                'id'      => 42,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'outputs',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'      => 43,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'outputs',
                'action'  => 'edit',
                'rule' => 'allow'
            ],
            [
                'id'      => 44,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'outputs',
                'action'  => 'delete',
                'rule' => 'allow'
            ],
            [
                'id'      => 45,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'devices',
                'action'  => 'index',
                'rule' => 'allow'
            ],
            [
                'id'      => 46,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'devices',
                'action'  => 'view',
                'rule' => 'allow'
            ],
            [
                'id'      => 47,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'devices',
                'action'  => 'add',
                'rule' => 'allow'
            ],
            [
                'id'      => 48,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'devices',
                'action'  => 'edit',
                'rule' => 'allow'
            ],
            [
                'id'      => 49,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'devices',
                'action'  => 'delete',
                'rule' => 'allow'
            ],
            [
                'id'      => 50,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'zones',
                'action'  => 'index',
                'rule' => 'allow'
            ],
            [
                'id'      => 51,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'zones',
                'action'  => 'view',
                'rule' => 'allow'
            ],
            [
                'id'      => 52,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'zones',
                'action'  => 'add',
                'rule' => 'allow'
            ],
            [
                'id'      => 53,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'zones',
                'action'  => 'edit',
                'rule' => 'allow'
            ],
            [
                'id'      => 54,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'zones',
                'action'  => 'delete',
                'rule' => 'allow'
            ],
            [
                'id'      => 55,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'datapoints',
                'action'  => 'recent',
                'rule' => 'allow'
            ],
            [
                'id'      => 56,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'recipeentries',
                'action'  => 'add',
                'rule' => 'allow'
            ],
            [
                'id'      => 57,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'recipeentries',
                'action'  => 'edit',
                'rule' => 'allow'
            ],
            [
                'id'      => 58,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'recipeentries',
                'action'  => 'delete',
                'rule' => 'allow'
            ],
            [
                'id'      => 59,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'charts',
                'action'  => 'grafana',
                'rule' => 'allow'
            ],
            [
                'id'      => 60,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'charts',
                'action'  => 'index',
                'rule' => 'allow'
            ],
            [
                'id'    => 62,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'batchrecipeentries',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'    => 63,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'floorplans',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'    => 64,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'usercontactmethods',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'    => 65,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'setpoints',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'    => 66,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'users',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'    => 67,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'tasks',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'    => 68,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'recipes',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'    => 69,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'pages',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'      => 70,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'plants',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'      => 71,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'zones',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'      => 72,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'photos',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'      => 73,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'notes',
                'action'  => '*',
                'rule' => 'allow'
            ],
            [
                'id'      => 74,
                'created'       => date(DATE_ATOM),
                'modified'    => date(DATE_ATOM),
                'controller'  => 'charts',
                'action'  => '*',
                'rule' => 'allow'
            ]
        ];

        $table = $this->table('acls');
        $table->insert($data)->save();
    }
}
