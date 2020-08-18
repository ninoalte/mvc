<?php

/*PDO CLASS

*Connect to database
*bind values
*return rows and results
*/

/**
 *
 */
class Database {

  private $host = DB_HOST;    //kommt von config.php
  private $user = DB_USER;    //kommt von config.php
  private $pass = DB_PASS;    //kommt von config.php
  private $dbname = DB_NAME;  //kommt von config.php


//diese Variablen werden unten verwendet
  private $dbh; //bekommt die PDO Klasse
  private $stmt; //
  private $error; // bekommt die error Message bei der catch funktion


  function __construct(){
        // set dsn
        $dsn='mysql:host=' . $this->host . ';dbname=' . $this->dbname; //ein string mit dem man bei pdo anklopft
        $options = array(
          PDO::ATTR_PERSISTENT => true,
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        );
        //CReate PDO instance
        try {
          $this->dbh=new PDO($dsn, $this->user, $this->pass, $options);
        }
        //bei Fehler
        catch(PDOException $e) {
          $this->error = $e->getMessage();
          echo $this->error;
        }
  } //constructor endet hier

  //Prepare statement with query
  public function query($sql) {
    $this->stmt = $this->dbh->prepare($sql); //prepare() Prepares a statement for execution and returns a statement object
  }

  //bind values
  public function bind($param, $value, $type = null){

        if (is_null($type)){
          switch (true) {
            case is_int($value):
              $type = PDO::PARAM_INT;
              break;
            case is_bool($value):
              $type = PDO::PARAM_BOOL;
              break;
            case is_null($value):
              $type = PDO::PARAM_NULL;
              break;
            default:
              $type = PDO::PARAM_STR;
            }
          }
        $this->stmt->bindValue($param, $value, $type); //
      	}

//execute the prepared statement
    public function execute(){
    return $this->stmt->execute();
    }

  //get result set as array of object

  public function resultSet(){
    $this->execute(); //this->execute weil wir die selbstdefiniert methode starten wollen (siehe gleich darüber)
    return $this->stmt->fetchAll(PDO::FETCH_OBJ);
  }

  // get single record as object
  public function resultSingle(){
    $this->execute();
    return $this->stmt->fetch(PDO::FETCH_OBJ);
  }

  public function rowCount(){
    return $this->stmt->rowCount();
  }

}
