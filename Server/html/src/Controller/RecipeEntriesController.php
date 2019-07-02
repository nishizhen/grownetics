<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * RecipeEntries Controller
 *
 * @property \App\Model\Table\RecipeEntriesTable $RecipeEntries
 */
class RecipeEntriesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
       
        $recipeEntries = $this->paginate($this->RecipeEntries, [
            'contain' => ['Recipes']]
        );

        $this->set(compact('recipeEntries'));
        $this->set('_serialize', ['recipeEntries']);
    }

    /**
     * View method
     *
     * @param string|null $id Recipe Entry id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $recipeEntry = $this->RecipeEntries->get($id, [
            'contain' => ['Zones', 'Recipes', 'BatchRecipeEntries']
        ]);

        $this->set('recipeEntry', $recipeEntry);
        $this->set('_serialize', ['recipeEntry']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->loadModel('Zones');
        $this->autoRender = false;
        $recipeEntry = $this->RecipeEntries->newEntity();
        if ($this->request->is('post')) {
            $recipeEntry = $this->RecipeEntries->patchEntity($recipeEntry, $this->request->data);
            if ($this->RecipeEntries->save($recipeEntry)) {
                $this->Flash->success(__('The recipe entry has been saved.'));
                return $this->redirect(['controller' => 'Recipes', 'action' => 'view', $recipeEntry->recipe_id]);
            } else {
                $this->Flash->error(__('The recipe entry could not be saved. Please, try again.'));
                return $this->redirect(['controller' => 'Recipes', 'action' => 'view', $recipeEntry->recipe_id]);
            }
        }
        $zone_types = $this->Zones->enums['plant_zone_types'];
        $recipes = $this->RecipeEntries->Recipes->find('list', ['limit' => 200]);

        $this->set(compact('recipeEntry', 'zone_types', 'recipes'));
        $this->set('_serialize', ['recipeEntry']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Recipe Entry id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->loadModel('Zones');
        $this->loadModel('Tasks');
        $recipeEntry = $this->RecipeEntries->get($id, ['contain' => ['Recipes']]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $recipeEntry = $this->RecipeEntries->patchEntity($recipeEntry, $this->request->data);

            if ($this->RecipeEntries->save($recipeEntry)) {
                $this->Flash->success(__('The recipe entry has been edited.'));

                return $this->redirect(['controller' => 'Recipes', 'action' => 'view', $recipeEntry->recipe_id]);
            } else {
                $this->Flash->error(__('The recipe entry could not be saved. Please, try again.'));
            }
        }
        $zoneTypes = $this->Zones->enums['plant_zone_types'];
        $types = [];
        foreach ($zoneTypes as $type) {
            $types += [$this->Zones->enumValueToKey('plant_zone_types', $type) => $type];
        }
        $taskTypes = $this->Tasks->enums['type'];
        $this->set(compact('recipeEntry', 'types', 'taskTypes'));
        $this->set('_serialize', ['recipeEntry', 'types', 'taskTypes']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Recipe Entry id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        
        $this->request->allowMethod(['post', 'delete']);
        $recipeEntry = $this->RecipeEntries->get($id);
        $recipe_id = $recipeEntry->recipe_id;
        if ($this->RecipeEntries->delete($recipeEntry)) {
            $this->Flash->success(__('The recipe entry has been deleted.'));
        } else {
            $this->Flash->error(__('The recipe entry could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller'=>'recipes','action' => 'view',$recipe_id]);
    }
}
