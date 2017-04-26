<?php

class MageProfis_Spam_Helper_Data
extends Mage_Core_Helper_Data
{
    /**
     * 
     * @param string $ip
     * @return boolean
     */
    public function isPenalty($ip)
    {
        $sql = $this->_getConnection()
                ->select()
                ->from($this->_getTableName('mpspam/penalty'), array('penalty'))
                ->where('ip = ?', $ip)
                ->limit(1)
        ;
        $result = (int) $this->_getConnection()->fetchOne($sql);
        if ($result > 10)
        {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param string $ua
     * @return boolean
     */
    public function checkUserAgent($ua)
    {
        $data = array(
            'Firefox/7.0.1',
        );
        foreach ($data as $_data)
        {
            if (stristr($ua, $_data))
            {
                return false;
                break;
            }
        }
        return true;
    }

    /**
     * 
     * @param string $ip
     * @return MageProfis_Spam_Helper_Data
     */
    public function setPenaltyRequest($ip)
    {
        $tbl = $this->_getTableName('mpspam/penalty');
        $ip = $this->_getConnection()->quoteInto('?', $ip);
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO {$tbl} (ip, penalty, created_at) VALUES ({$ip}, 1, '{$date}') ON DUPLICATE KEY UPDATE penalty = penalty + 1;";
        $this->_getConnection('core_write')->query($query);
        return $this;
    }

    /**
     * 
     * @return $this
     */
    public function removeOld()
    {
        $date = date('Y-m-d H:i:s', (time() - 86400));
        $where = $this->_getConnection()->quoteInto('created_at <= ?', $date);
        $sql = $this->_getConnection('core_write')
                ->delete($this->_getTableName('mpspam/penalty'), $where);
        return $this;
    }

    /**
     * 
     * @return Mage_Core_Model_Resource
     */
    protected function _resource()
    {
        return Mage::getSingleton('core/resource');
    }

    /**
     * 
     * @param string $name
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection($name = 'core_read')
    {
        return $this->_resource()->getConnection($name);
    }

    /**
     * 
     * @param string $modelEntity
     * @return string
     */
    protected function _getTableName($modelEntity)
    {
        return $this->_resource()->getTableName($modelEntity);
    }
}