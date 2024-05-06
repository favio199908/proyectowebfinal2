<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        // Añade logout a la lista de actiones permitidas.
        $this->Auth->allow(['logout', 'add','index','view','add','edit','delete']);
    }
    public function logout()
    {
        // Muestra un mensaje de éxito indicando que el usuario ha cerrado sesión
        $this->Flash->success('Ahora estás deslogueado.');
        
        // Redirige al usuario a la página de inicio de sesión después de cerrar sesión
        return $this->redirect($this->Auth->logout());
    }   
    
    public function login()
    {
        // Verificamos si la solicitud es de tipo POST (es decir, si se envió el formulario de inicio de sesión)
        if ($this->request->is('post')) {
            // Intentamos identificar al usuario utilizando el componente Auth
            $user = $this->Auth->identify();
            
            // Si se identifica al usuario correctamente
            if ($user) {
                // Establecemos la sesión del usuario y lo redirigimos a la URL a la que intentaba acceder
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            
            // Si no se identifica al usuario, mostramos un mensaje de error
            $this->Flash->error('Tu usuario o contraseña es incorrecta.');
        }
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Bookmarks'],
            
        ]);

        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el usuario. Inténtalo de nuevo.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /*
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        
        // Desactivar restricciones de clave externa
        $this->Users->getConnection()->disableForeignKeys();

        // Eliminar usuario
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        // Reactivar restricciones de clave externa
        $this->Users->getConnection()->enableForeignKeys();

        return $this->redirect(['action' => 'index']);
    }

}