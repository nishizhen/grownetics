<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Photos Controller
 *
 * @property \App\Model\Table\PhotosTable $Photos
 *
 * @method \App\Model\Entity\Photo[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PhotosController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Notes']
        ];
        $photos = $this->paginate($this->Photos);

        $this->set(compact('photos'));
    }

    /**
     * View method
     *
     * @param string|null $id Photo id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $photo = $this->Photos->get($id, [
            'contain' => ['Notes']
        ]);

        $this->set('photo', $photo);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $photo = null;
        $this->set(compact('photo'));
        $this->loadModel('Notes');
        $uid = $this->Auth->user('id');

        if ( $this->request->data['photo']['tmp_name'] != '' ) {
            $filename=basename($this->request->data['photo']['name']);
            $full_filename=date("Y-M-D").$uid."-".$filename;
            if (move_uploaded_file($this->request->data['photo']['tmp_name'], WWW_ROOT . 'uploads' . DS . $full_filename)) {
                chmod(WWW_ROOT . 'uploads' . DS . $full_filename, 0755);
                $this->request->data['photo'] = $full_filename;

                if ( isset($this->request->data['note']) ) {
                    $noteData = $this->Notes->newEntity([
                        'cultivar_id' => $this->request->data['cultivar_id'],
                        'batch_id' => $this->request->data['batch_id'],
                        'zone_id' => $this->request->data['zone_id'],
                        'plant_id' => $this->request->data['plant_id'],
                        'note' => $this->request->data['note'],
                        'user_id' => $uid,
                        'photo_name' => $full_filename
                    ]);
                    if ($this->Notes->save($noteData)) {
                        $this->redirect($this->referer());
                        $this->Flash->success(__('The photo and note have been uploaded successfully'));
                    } else {
                        $this->Flash->error(__('The photo and note could not be saved. Please, try again.'));
                    }
                }
            }
        } else {
            // add note no photo
            $noteData = $this->Notes->newEntity([
                'cultivar_id' => $this->request->data['cultivar_id'],
                'batch_id' => $this->request->data['batch_id'],
                'zone_id' => $this->request->data['zone_id'],
                'plant_id' => $this->request->data['plant_id'],
                'note' => $this->request->data['note'],
                'user_id' => $uid,
            ]);
            if ($this->Notes->save($noteData)) {
                $this->redirect($this->referer());
                $this->Flash->success(__('The note has been uploaded successfully'));
            } else {
                $this->Flash->error(__('The note could not be saved. Please, try again.'));
            }
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Photo id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $photo = $this->Photos->get($id, [
            'contain' => ['Notes']
        ]);
        $uid = $this->Auth->user('id');
        if ($this->request->is(['patch', 'post', 'put'])) {
            if ( $this->request->data['photo']['tmp_name'] != '' ) {
                $filename = basename($this->request->data['photo']['name']);
                $full_filename = date("Y-M-D") . $uid . "-" . $filename;
                if (move_uploaded_file($this->request->data['photo']['tmp_name'], WWW_ROOT . 'uploads' . DS . $full_filename)) {
                    chmod(WWW_ROOT . 'uploads' . DS . $full_filename, 0755);
                    $this->request->data['photo_name'] = $full_filename;
                }

                $photo = $this->Photos->patchEntity($photo, $this->request->data);

                if ($this->Photos->save($photo)) {
                    $this->Flash->success(__('The photo has been saved.'));

                    return count($photo->notes) > 0 ? $this->redirect(['controller' => 'notes', 'action' => 'edit', $photo->notes[0]['id']]) : $this->redirect(['controller' => 'photos', 'action' => 'edit', $photo->id]);
                }
                $this->Flash->error(__('The photo could not be saved. Please, try again.'));
            }
        }
        $notes = $this->Photos->Notes->find('list', ['limit' => 200]);
        $this->set(compact('photo', 'notes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Photo id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $photo = $this->Photos->get($id, [
            'contain' => ['Notes']
        ]);
        $noteId = false;
        if ( count($photo->notes) > 0 ) {
            $noteId = $photo->notes[0]['id'];
        }
        if ($this->Photos->delete($photo)) {
            $this->Flash->success(__('The photo has been deleted.'));
        } else {
            $this->Flash->error(__('The photo could not be deleted. Please, try again.'));
        }
        return $this->redirect(['controller' => 'notes', 'action' => 'edit', $noteId]);
    }

    public function load($id = null)
    {
        $photo = $this->Photos->get($id, [
            'contain' => ['Notes']
        ]);
            // Create an image resource: $image ...
        $response = $this->response->withFile(WWW_ROOT . 'uploads' . DS . $photo->id . '.' . $photo->extension);
        return $response;
    }

    /**
     * largeView method
     *
     * @param string|null $id Photo id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function rawImage($id = null)
    {
        $this->viewBuilder()->layout('blank');
        $photo = $this->Photos->get($id, [
            'contain' => ['Notes']
        ]);

        $this->set('photo', $photo);
    }
}
