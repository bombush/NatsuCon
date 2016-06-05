<?php

namespace Natsu\Model;


use Nette;



class EntityModel extends \Nette\Object{
    /**
     * @var \DibiConnection
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
    
    public function fetchWhere($where){
        $sql = "SELECT * FROM {$this->table}";
        foreach($where as $column => $intValue)
           $sql .= " WHERE ".$column." = $intValue";
        
        
        return $this->database->query($sql)->fetchAll();
    }

    public function getPrimary($id = NULL){
        return $this->table($id);
    }

    public function fetchAll(){
        return $this->table();
    }

    public function insert($values){
        if ( empty( $this->table ) )
            throw new \Natsu\Model\Exception( 'Cannot insert - missing table name in model class' );
        //return $this->database->lastInsertId($this->table);
        //die('insert');
        $this->database->query("INSERT INTO [$this->table]", $values);
        return $this->database->getInsertId();
        //$context = new Context($this->database);
        //$context->table($this->table)->insert($values);
        //return $context->getInsertId();
    }
    
    public function log($userId, $data){
        $data['userId'] = $userId; 
        $this->database->query("INSERT INTO [statuslog]", $data);
    }

    public function update($values){
          if(empty($this->table))
              throw new \Natsu\Model\Exception('Cannot update - missing table name in model class');
          //die('update');
         $this->database->query("UPDATE `$this->table` SET ", $values, ' WHERE `id`= ?', $values->id);


         // $context = new Nette\Database\Context($this->database);
         // $context->table($this->table)->getPrimary($values->id)->update($values);
      //  $this->table()->wherePrimary($values->id)->update($values);
    }

    /**
     * @param $id numeric
     *
     * @throws Exception
     */
    public function deleteId($id) {
        if(!is_numeric($id))
            throw new Exception('Wrong DELETE parameter: Non-numeric ID not allowed');

        $this->database->delete($this->table)->where('id = ?', (int)$id)->execute() ;
    }




    /*
    public function fetch($sql){
        $this->database->query($statement)
    }
     *
     */

    /**
     * @TODO: pokud neexistuje, vratit instanci EntityModel a nastavit ji nazev tabulky podle $className?
     * @param $className
     *
     * @return mixed
     */
    public function reflection($className){
        $className = ucfirst($className);
        $className = "\Natsu\Model\\".$className."Model";
        return new $className($this->database);
    }

    /**
     * Begin transaction
     * @param null $savepoint
     *
     * @throws \DibiException
     */
    public function begin($savepoint = NULL) {
        $this->database->begin($savepoint);
    }

    /**
     * Commit transaction
     *
     * @param null $savepoint
     *
     * @throws \DibiException
     */
    public function commit($savepoint = NULL) {
        $this->database->commit($savepoint);
    }

    /**
     * Rollback transaction
     *
     * @param null $savepoint
     *
     * @throws \DibiException
     */
    public function rollback($savepoint = NULL) {
        $this->database->rollback($savepoint);
    }
}

class Exception extends \Exception {};
?>
