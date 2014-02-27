<?php
App::uses('AppController', 'Controller');
/**
 * Issues Controller
 *
 * @property Issue $Issue
 * @property PaginatorComponent $Paginator
 */
class IssuesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Issue->recursive = 0;
		$this->set('issues', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Issue->recursive = -1;
		if (!$this->Issue->exists($id)) {
			throw new NotFoundException(__('Invalid issue'));
		}
		$options = array(
			'conditions' => array('Issue.' . $this->Issue->primaryKey => $id),
			'contain' => array('Project', 'User', 'ModifiedUser', 'Agent')
		);
		$this->set('issue', $this->Issue->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Issue->create();
			if ($this->Issue->save($this->request->data)) {
				$this->Session->setFlash(__('The issue has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The issue could not be saved. Please, try again.'));
			}
		}
		$users = $this->Issue->User->find('list');
		$modifiedUsers = $this->Issue->ModifiedUser->find('list');
		$projects = $this->Issue->Project->find('list');
		$this->set(compact('users', 'modifiedUsers', 'projects'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Issue->exists($id)) {
			throw new NotFoundException(__('Invalid issue'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Issue->save($this->request->data)) {
				$this->Session->setFlash(__('The issue has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The issue could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Issue.' . $this->Issue->primaryKey => $id));
			$this->request->data = $this->Issue->find('first', $options);
		}
		$users = $this->Issue->User->find('list');

		$this->Issue->Project->recursive = -1;
		$project = $this->Issue->Project->find('first', array(
			'conditions' => array('Project.id' => $this->request->data['Issue']['project_id']),
			'contain' => array('User')
		));

		$agents = $this->Issue->User->find('list', array('conditions' => array(
			'User.role' => 'agent',
			'id' => Set::extract('/User/id', $project)
		)));

		$modifiedUsers = $this->Issue->ModifiedUser->find('list');
		$projects = $this->Issue->Project->find('list');
		$this->set(compact('users', 'modifiedUsers', 'projects', 'agents'));
	}

	public function assignTo($id, $userId) {
		$this->Issue->id = $id;
		if (!$this->Issue->exists()){
			throw new NotFoundException(__('Invalid issue'));
		}
		$issue = $this->Issue->read();
		$issue['Agent']['id'] = $userId;
		if ($this->Issue->saveAll($issue)){
			$this->Session->setFlash(__('The issue has been assigned to user #'.$userId));
		}
		else {
			$this->Session->setFlash(__('The issue could not been assigned'));
		}
		return $this->redirect(array('action' => 'view', $id));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Issue->id = $id;
		if (!$this->Issue->exists()) {
			throw new NotFoundException(__('Invalid issue'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Issue->delete()) {
			$this->Session->setFlash(__('The issue has been deleted.'));
		} else {
			$this->Session->setFlash(__('The issue could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
