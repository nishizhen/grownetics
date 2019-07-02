<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Notes Controller
 *
 * @property \App\Model\Table\NotesTable $Notes
 *
 * @method \App\Model\Entity\Note[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NotesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'HarvestBatches' => ['Cultivars'], 'Cultivars', 'Zones', 'Photos']
        ];
        $notes = $this->paginate($this->Notes);

        $this->set(compact('notes'));
    }

    /**
     * View method
     *
     * @param string|null $id Note id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
//        $note = $this->Notes->get($id, [
//            'contain' => ['Users', 'HarvestBatches', 'Cultivars', 'Zones', 'Plants' ]
//        ]);
        $note = $this->Notes->get($id, []);

        $this->set('note', $note);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->loadModel('Plants');
        $this->loadModel('HarvestBatches');
        $this->loadModel('NotesPlants');
        $note = $this->Notes->newEntity();
        if ($this->request->is('post')) {
            $note = $this->Notes->patchEntity($note, $this->request->getData(), ['validate' => false]);
            $isPlantNote = false;
            // print_r($this->request); die();
            if ($this->request->data['photo_name']['name'] != '') {
                $filename = basename($this->request->data['photo_name']['name']);
                $full_filename = date("Y-M-D").$this->Auth->user('id')."-".$filename;
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
            
                $photo = [
                    'photos' => [
                        [
                            'photo_name' => $full_filename,
                            'extension' => $extension
                        ]
                    ]
                ];  
                $note = $this->Notes->patchEntity($note, $photo, ['associated' => [
                        'Photos' => ['validate' => false] 
                ]]);
            }

            $note->user_id = $this->Auth->user('id');
            switch ($this->request->data['modelName']) {
                case 'Batch':
                    $harvest_batch = $this->HarvestBatches->get($note->batch_id);
                    $note->cultivar_id = $harvest_batch->cultivar_id;
                    if ($harvest_batch->current_zone) {
                        $note->zone_id = $harvest_batch->current_zone->id;
                    }
                    break;
                case 'Plant':
                    $plant = $this->Plants->get($note->plant_id);
                    $harvest_batch = $this->HarvestBatches->get($plant->harvest_batch_id);
                    $note->zone_id = $plant->zone_id;
                    $note->batch_id = $plant->harvest_batch_id;
                    $note->cultivar_id = $harvest_batch->cultivar_id;
                    $isPlantNote = true;
                    break;
            }

            if ($this->Notes->save($note)) {
                $note->note_id = $note->id;
                if ($isPlantNote) {
                    $NotesPlantsEntity = $this->NotesPlants->newEntity();
                    $plantNote = $this->NotesPlants->patchEntity($NotesPlantsEntity, $note->toArray(), ['associated' => [
                        'Notes' => ['validate' => false]
                    ]]);
                    if ( !$this->NotesPlants->save($plantNote) ) {
                        $this->Flash->Error(__('The note could not be saved. Please, try again.'));
                        return $this->redirect($this->referer());
                    }
                }
                if ($note->photos != null) {
                    if ( $this->movePhotoAndHandlePermissions($this->request->data['photo_name']['tmp_name'], $note->photos[0]) ) {
                        $this->Flash->success('The note has been saved.');
                        return $this->redirect($this->referer());
                    }
                    $this->Flash->error(__('The photo could not be saved. Please, try again.'));
                    return $this->redirect($this->referer());
                } else {
                    $this->Flash->success(__('The note has been saved.'));
                    return $this->redirect($this->referer());
                }
            }
            $this->Flash->error(__('The note could not be saved. Please, try again.'));
            return $this->redirect($this->referer());
        }
        $users = $this->Notes->Users->find('list', ['limit' => 200]);
        $harvest_batches = $this->Notes->HarvestBatches->find('list', ['limit' => 200]);
        $cultivars = $this->Notes->Cultivars->find('list', ['limit' => 200]);
        $zones = $this->Notes->Zones->find('list', ['limit' => 200]);
        $plants = $this->Notes->Plants->find('list', ['limit' => 200]);
        $this->set(compact('note', 'users', 'harvest_batches', 'cultivars', 'zones', 'plants'));
    }

    public function movePhotoAndHandlePermissions($request_data, $notes_photos) {
        $photo_id = $notes_photos->id;
        if (move_uploaded_file($request_data, WWW_ROOT . 'uploads' . DS . $photo_id . '.' . $notes_photos->extension)) {
            chmod(WWW_ROOT . 'uploads' . DS . $photo_id . '.' . $notes_photos->extension, 0755);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Note id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->loadModel('Zones');
        $this->loadModel('Cultivars');
        $this->loadModel('HarvestBatches');
        $this->loadModel('Photos');
        $this->loadModel('NotesPhotos');
        $note = $this->Notes->get($id, [
            'contain' => ['Plants', 'Photos', 'Cultivars', 'Zones', 'HarvestBatches' => ['Cultivars']
            , 'Users']
        ]);
        $reff = $this->referer();
        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($this->request->data['photo_name']['name'] != '') {
                $filename = basename($this->request->data['photo_name']['name']);
                $full_filename = date("Y-M-D").$this->Auth->user('id')."-".$filename;
                if (move_uploaded_file($this->request->data['photo_name']['tmp_name'], WWW_ROOT . 'photos' . DS . $full_filename)) {
                    chmod(WWW_ROOT . 'photos' . DS . $full_filename, 0755);
                    $this->request->data['photo_name'] = $full_filename;
                }

                $photo = $this->Photos->newEntity([
                    'photo_name' => $full_filename
                ]);
                if ($this->Photos->save($photo) ) {
                    $notePhotos = $this->NotesPhotos->newEntity([
                        'note_id' => $id,
                        'photo_id' => $photo->id
                    ]);
                    $this->NotesPhotos->save($notePhotos);
                }
            }
            $note = $this->Notes->patchEntity($note, $this->request->getData());
            if ($this->Notes->save($note)) {
                $this->Flash->success(__('The note has been saved.'));
                return $this->redirect($this->referer());
            }
            $this->Flash->error(__('The note could not be saved. Please, try again.'));
        }
        $users = $this->Notes->Users->find('list', ['limit' => 200]);
        $harvest_batches = $this->HarvestBatches->find('all', ['limit' => 200, 'fields' => ['batch_number', 'Cultivars.label', 'id'], 'contain' => ['Cultivars']]);
        $cultivars = $this->Cultivars->find('list', ['limit' => 200]);
        $zones = $this->Zones->find('list', ['limit' => 200]);
        $plants = $this->Notes->Plants->find('list', ['limit' => 200]);
        $this->set(compact('note', 'users', 'harvest_batches', 'cultivars', 'zones', 'plants', 'reff'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Note id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $note = $this->Notes->get($id);
        if ($this->Notes->delete($note)) {
            $this->Flash->success(__('The note has been deleted.'));
            $this->redirect($this->referer());
        } else {
            $this->Flash->error(__('The note could not be deleted. Please, try again.'));
            $this->redirect($this->referer());
        }
        return $this->redirect(['controller' => 'Notes', 'action' => 'index']);
    }
}
