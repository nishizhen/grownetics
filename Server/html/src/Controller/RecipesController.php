<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Recipes Controller
 *
 * @property \App\Model\Table\RecipesTable $Recipes
 * @property \App\Model\Table\ZonesTable $Zones
 * @property \App\Model\Table\RecipeEntriesTable $RecipeEntries
 */
class RecipesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['RecipeEntries'],
            'order' => ['id'=>'asc']
        ];
        $recipes = $this->paginate($this->Recipes);

        $this->set(compact('recipes'));
        $this->set('_serialize', ['recipes']);
    }

    /**
     * View method
     *
     * @param string|null $id Recipe id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->loadModel('Zones');
        $this->loadModel('RecipeEntries');
        $this->loadModel('Tasks');
        $recipe = $this->Recipes->get($id);   
        $recipeEntries = $this->paginate($this->RecipeEntries, [
            'contain' => ['Recipes'],
            'conditions' => ['recipe_id' => $id]
            ]
        )->toArray();
        foreach($recipeEntries as $key => $entry) {
            $entry['zone_type_label'] = $this->Zones->enumKeyToValue('plant_zone_types', $entry->plant_zone_type_id);
            $zones = $this->Zones->find('all', ['conditions' => ['plant_zone_type_id' => $entry->plant_zone_type_id, 'zone_type_id' => $this->Zones->enumValueToKey('zone_types', 'Room')]])->toArray();
            $entry['plant_zones'] = $zones;
           if($entry->task_type_id != null) {
               unset($recipeEntries[$key]);
           }
        }
        $recipeEntry = $this->RecipeEntries->newEntity();
        $zones = $this->Zones->find('all', ['limit' => 200])->toArray();
        $plant_zone_types = $this->Zones->enums['plant_zone_types'];
        $this->set(compact('recipeEntries','plant_zone_types', 'zones', 'recipeEntry', 'recipe'));
        $this->set('_serialize', ['recipeEntries', 'recipeEntry', 'recipe']);


    }

    public function getTaskRelatedEntries() {
        $this->loadModel('RecipeEntries');
        $taskEntries = $this->RecipeEntries->find('all', ['conditions' => ['parent_recipe_entry_id' => $this->request->data['recipe_id']]]);
        $this->set(compact('taskEntries'));
        $this->set('_serialize', ['taskEntries']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $recipe = $this->Recipes->newEntity();
        if ($this->request->is('post')) {
            $recipe = $this->Recipes->patchEntity($recipe, $this->request->data);
            if ($this->Recipes->save($recipe)) {
                $this->Flash->success(__('The recipe has been saved.'));

                return $this->redirect(['action' => 'view', $recipe->id]);
            } else {
                $this->Flash->error(__('The recipe could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('recipe'));
        $this->set('_serialize', ['recipe']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Recipe id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $recipe = $this->Recipes->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $recipe = $this->Recipes->patchEntity($recipe, $this->request->data);
            if ($this->Recipes->save($recipe)) {
                $this->Flash->success(__('The recipe has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            } else {
                $this->Flash->error(__('The recipe could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('recipe'));
        $this->set('_serialize', ['recipe']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Recipe id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $recipe = $this->Recipes->get($id);
        if ($this->Recipes->delete($recipe)) {
            $this->Flash->success(__('The recipe has been deleted.'));
        } else {
            $this->Flash->error(__('The recipe could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
