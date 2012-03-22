<?php
class Litle_LitleEcheck_Model_Config
{
    public function getAccountTypes()
    {
        $types = array('Checking' => 'Checking', 'Savings' => 'Savings','Corporate'=>'Corporate','Corp Savings' => 'Corp Savings');
        return $types;
    }
}
