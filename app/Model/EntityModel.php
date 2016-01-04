<?php

namespace Natsu\Model;


use Nette;



class EntityModel extends \Nette\Object{
    /**
     * @var DibiConnection
     */
    public $database;
    public $table;

    public function  __construct(\DibiConnection $database) {
        $this->database = $database;
       // print_r($this->database);
       // exit;
    }

    public function setTable($table){
        $this->table = $table;
        return $this;
    }

    public function table($id = NULL){
        $sql = "SELECT * FROM {$this->table}";
        if(!is_null($id)){
           $sql .= " WHERE id = $id";
        }
        //echo $sql;
        return $id == NULL? $this->database->query($sql)->fetchAll() : $this->database->query($sql)->fetch();
    }

    public function getPrimary($id = NULL){
        return $this->table($id);
    }

    public function fetchAll(){
        return $this->table();
    }

    public function insert($values){
        //return $this->database->lastInsertId($this->table);
        //die('insert');
        $this->database->query("INSERT INTO $this->table SET ", $values);
        return $this->database->getInsertId();
        //$context = new Context($this->database);
        //$context->table($this->table)->insert($values);
        //return $context->getInsertId();
    }

    public function update($values){
          //die('update');
         $this->database->query("UPDATE $this->table SET ", $values, ' WHERE `id`= ?', $values->id);


         // $context = new Nette\Database\Context($this->database);
         // $context->table($this->table)->getPrimary($values->id)->update($values);
      //  $this->table()->wherePrimary($values->id)->update($values);
    }


    /*
    public function fetch($sql){
        $this->database->query($statement)
    }
     *
     */

    public function reflection($className){
        $className = ucfirst($className);
        $className = "\Natsu\Model\\".$className."Model";
        return new $className($this->database);
    }

}
?>
