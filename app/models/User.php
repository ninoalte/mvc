<?php
class User {     //Klasse wird kreiert
  private $db;

  public function __construct(){
    $this->db = new Database;  //eine neue Database Klasse wird initialisiert .. siehe Database.php
  } //constructor endet hier

  //register user
  public function register($data){
    $this->db->query('INSERT INTO users(name, email, password) VALUES (:name, :email, :password)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        //execute
        if($this->db->execute()){
          return true;
        } else {
          return false;
        }
  }

//Login user

  public function login($email, $password){
    $this->db->query('SELECT * FROM users WHERE email = :email');
    $this->db->bind(':email', $email);

    $row = $this->db->resultSingle();

    $hashed_password = $row->password;
    if(password_verify($password, $hashed_password)){
      return $row;
    } else {
      return false;
    }
  }

  //find User by email
  public function findUserByEmail($email){
    var_export($email);
    $this->db->query('SELECT * FROM users WHERE email= :email');
    $this->db->bind(':email', $email);

  $row = $this->db->resultSingle();
 print_r($row) ;
    //check row

    if($this->db->rowCount() > 0){
      return true;
    } else {
      return false;
    }
  }

//get user by id
  public function getUserById($id){

    $this->db->query('SELECT * FROM users WHERE id= :id');
    $this->db->bind(':id', $id);

  $row = $this->db->resultSingle();

    //check row


      return $row;

  }

}
