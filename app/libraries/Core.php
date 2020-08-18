<?php

// Creates URL &loads core controller
//  URL FORMAT - /controller/method/params

class Core {
  protected $currentController = 'Pages';
  protected $currentMethod = 'index';
  protected $params = [];

//constructor runs automatically when class is instantiated
  public function __construct() {

    $url=$this->getURL();  //bekomm den URL array
    if (isset($url[0])) { //wenn URL[0] ist vorhanden (erst vorhanden wenn nicht auf start seite)
    if (file_exists('../app/controllers/' . ucwords($url[0]). '.php')){ //ucwords wandelt ersten Buchstaben in Großbuchstaben, wenn es eine datei in der struktur mit dem namen gibt...
      $this->currentController = ucwords($url[0]); //dann setze den currController zu diesem wert
      unset($url[0]); //unset url[0] index, damit er bei nächsten seitenaufruf auch funktioniert
    }
  }

    //require the controller
    require_once '../app/controllers/'. $this->currentController.'.php'; //lade den gerade gesetzen Controller-Code


    //instatiate controller class
    $this->currentController= new $this->currentController; //jetzt bekommt currentController eine Klasse zugewiesen (vorher war nur der Name von url[0] gespeichert)

//wiederhole dieselben Schritte für die 1.variable
    //check for second part of url
    if(isset($url[1])){
      if(method_exists($this->currentController, $url[1])){ //schau in der currentController Klasse nach, ob eine passend Method vorhanden ist
        $this->currentMethod=$url[1]; //gib ihr den Method String
        //unset 1 Index
            unset($url[1]);
      }
    }
    //get params
    $this->params= $url ? array_values($url) : [];

    //call a callback with array of $params
    call_user_func_array([$this->currentController, $this->currentMethod],
      $this->params);
    //  print_r($url); //prints array of params
  } //constructor ends here



  public function getURL(){ //wird im constructor benutzt
    if(isset($_GET['url'])){ //$_GET nimmt die parameter von der URL
      $url= rtrim($_GET['url'],'/'); //rtrim entfernt leerzeichen vom ende des strings
      $url= filter_var($url, FILTER_SANITIZE_URL); //exkludie Zeichen wie Leerzeichen ua. -> "ab c" -> "abc"
      $url=explode('/', $url); //nach jedem / vergib einen neuen Array Index
      //print_r($url); //print $url to screen
      return $url;
    }
  }



}
