<?php
    App::uses('AppModel', 'Model');
    /**
     * Group Model
     *
     * @property User $User
     */
class CorpusGroup extends AppModel {
    public $belongsTo = array(
        'GroupInCorpusGroup' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
            'dependent' => true
        )
    );
    /*public $hasMany = array(
        'XMLObjectsInCorpusGroup' => array(
            'className' => 'XMLObject',
            'foreignKey' => 'x_m_l_object_id',
            'dependent' => true
        ),
        'GroupInCorpusGroup' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
            'dependent' => true
        ));*/
}