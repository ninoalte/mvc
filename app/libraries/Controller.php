<?php
  //this is the base controller, loads models and views

class Controller {
  //load models
  public function model($model){
    //require model file
    require_once '../app/models/'.$model . '.php';

    //instatiate models
    return new $model();

  }
  //load views
  public function view($view, $data = []){
    //check for the view file
    if (file_exists('../app/views/'. $view . '.php')){
      require_once '../app/views/'. $view . '.php';
    } else {
      die('View does not exist');
    }
  }

}
