<?php
    App::uses('AppModel', 'Model');
    /**
     * Group Model
     *
     * @property User $User
     */
class CorpusCreator extends AppModel {

    function beforeFilter() {
        parent::beforeFilter();
    }

    public $belongsTo =  array(
        'User' => array(
          'className' => 'User',
            'dependent' => true
        ));

    public $hasMany = array(
        'CreatedObjects' => array(
           'className' => 'XMLObject',
           'foreignKey' => 'name',
           'dependent' => true
        ));
}