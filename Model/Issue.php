<?php
App::uses('AppModel', 'Model');
/**
 * Issue Model
 *
 * @property User $User
 * @property ModifiedUser $ModifiedUser
 * @property Project $Project
 */
class Issue extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'modified_user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'title' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'description' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'project_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'status' => array(
			'valid' => array(
				'rule' => array(
					'inList' => array(
						'open', 
						'confirmed',
						'assigned',
						'closed'
					)
				)
			)
		),
		'solution' => array(
			'valid' => array(
				'rule' => array(
					'inList' => array(
						'resolved',
						'wontfix',
						'cantfix'
					)
				)
			)
		)
		'type' => array(
			'valid' => array(
				'rule' => array(
					'inList' => array(
						'bug',
						'enhancement',
						'feature-request'
					)
				)
			)
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ModifiedUser' => array(
			'className' => 'User',
			'foreignKey' => 'modified_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public $hasAndBelongsToMany = array(
		'Agent' => array(
			'className' => 'User'
		)
	);

	function beforeSave(){

		if (!isset($this->data['User']['user_id'])){
			// Try to find the user by email
			if (isset($this->data['User']['email'])){
				$user = $this->User->find('first', array('conditions' => array(
					'User.email' => $this->data['User']['email']
				)));
				if (!empty($user)) {
					$this->data['Issue']['user_id'] = $user['User']['id'];
				}
				else {
					$user = array(
						'email' => $this->data['User']['email'],
						'role' => 'guest'
					);
					$this->User->save($user);
				}
			}
		}

		if (!isset($this->data['Issue']['project_id'])){
			$url = parse_url(env('HTTP_REFERER'), PHP_URL_HOST);
			$project = $this->Project->findByurl($url);
			if (!empty($project)){
				$this->data['Issue']['project_id'] = $project['Project']['id'];
			}
			else {
				return false;
			}
		}

		return true;

	}
}
