<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Condroid;

/**
 * Description of DbSave
 *
 * @author yasuri
 */
class DbSimple {
    /**
    *
    * @var \DibiConnection
    */
    public $db;
    public $table;
    public $data;
    
    /**
     * 
     * @param type $table
     * @return \Custom\Condroid\DbSimple
     */
    function setTable($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * 
     * @param type $data
     * @return \Custom\Condroid\DbSimple
     */
    function setData($data) {
        $this->data = $data;
        return $this;
    }

        
    
public function insert(){
    $this->db->insert($this->table, $this->data)->execute();
    return $this->db->insertId;
    
    
}


public function getId($table, $key){
        $key = trim($key);
        return $this->db->select("id")->from($table)->where("title = ?", $key)->fetchSingle();
    }

}
