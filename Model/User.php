<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * User Model
 *
 * @property Issue $Issue
 */
class User extends AppModel {

	public $displayField = 'email';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'username' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty')
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email')
			),
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty')
			),
		),
		'lastname' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty')
			),
		),
		'role' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty')
			),
		),
	);


/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Issue' => array(
			'className' => 'Issue',
			'foreignKey' => 'user_id',
			'dependent' => false,
		)
	);

	public function beforeSave($options = array()){
		if (isset($this->data[$this->alias]['password'])){
			$passwordHasher = new SimplePasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash($this->data[$this->alias]['password']);
		}
		return true;
	}
}
