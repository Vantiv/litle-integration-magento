<?php
class Litle_LitleEcheck_Model_Config
{
    protected static $_methods;

    /**
     * Retrieve active system payments
     *
     * @param   mixed $store
     * @return  array
     */
    public function getActiveMethods($store=null)
    {
        $methods = array();
//         $config = Mage::getStoreConfig('echeck', $store);
//         foreach ($config as $code => $methodConfig) {
//             if (Mage::getStoreConfigFlag('echeck/'.$code.'/active', $store)) {
//                 $methods[$code] = $this->_getMethod($code, $methodConfig);
//             }
//         }
        return $methods;
    }

    /**
     * Retrieve all system payments
     *
     * @param mixed $store
     * @return array
     */
    public function getAllMethods($store=null)
    {
        $methods = array();
        $config = Mage::getStoreConfig('payment', $store);
        foreach ($config as $code => $methodConfig) {
            $methods[$code] = $this->_getMethod($code, $methodConfig);
        }
        return $methods;
    }

    protected function _getMethod($code, $config, $store=null)
    {
        if (isset(self::$_methods[$code])) {
            return self::$_methods[$code];
        }
        $modelName = $config['model'];
        $method = Mage::getModel($modelName);
        $method->setId($code)->setStore($store);
        self::$_methods[$code] = $method;
        return self::$_methods[$code];
    }

    /**
     * Retrieve array of credit card types
     *
     * @return array
     */
    public function getAccountTypes()
    {
        $types = array('Checking' => 'Checking', 'Savings' => 'Savings','Corporate'=>'Corporate','Corp Savings' => 'Corp Savings');
        return $types;
    }
}
