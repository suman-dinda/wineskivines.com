<?php
class User extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
 
    public function tableName()
    {
        return '{{user_master_list}}';
    }
    
    public function primaryKey()
    {
       return 'session_token';    
    }
}
/*end class*/