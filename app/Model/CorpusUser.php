<?php
App::uses('AppModel', 'Model');
/**
 * Group Model
 *
 * @property User $User
 */
class CorpusUser extends AppModel {

    public $belongsTo = array(
        'UserInCorpusUser' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true
        )
    );
    /*public $hasMany = array(
        'XMLObjectsInCorpusUser' => array(
            'className' => 'XMLObject',
            'foreignKey' => 'x_m_l_object_id',
            'dependent' => true
        ),
        'UserInCorpusUser' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true
        ));*/
}