<?php

  //url after mvcname/...
class Users extends Controller {
  public function __construct(){
    $this->userModel = $this->model('User');
  }

  //in URL after /users/..
  public function register(){
    //check for post
    if($_SERVER['REQUEST_METHOD']=='POST'){
      $Error = FALSE;



      //Process form
//sanitize POST Data
      $_POST= filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'confirm_password' => trim($_POST['confirm_password']),
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => '',
        'error' => $Error
      ];


//validate email
  if(empty($data['email'])){

      $Error=TRUE;
      $data['email_err']='Please enter email';
      }  else {
        // check email

        if($this->userModel->findUserByEmail($data['email'])){
            $data['email_err']='Email is already taken';
              $Error=TRUE;
        }
      }


  // Validate name
  if(empty($data['name'])){
    $data['name_err']='Please enter name';
      $Error=TRUE;
  }
  //Validate password
  if(empty($data['password'])){
      $data['password_err']='Please enter password';
      $Error=TRUE;
  } elseif (strlen($data['password'])< 6){
      $data['password_err']='Password must be at least 6 characters';
      $Error=TRUE;
  }
//Validate Confirm Password
  if(empty($data['confirm_password'])){
    $data['confirm_password_err']='Please type in the password again';
        $Error=TRUE;
      } else {
      if($data['password']!=$data['confirm_password']){
          $data['confirm_password_err']='Passwords do not match';
          $Error=TRUE;
      }
  }

    if ($Error==FALSE) {
    //   //Validated
      // var_export($Error);
      // echo gettype($Error);


      //hash the password
      $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

      //reggister user
      if($this->userModel->register($data)){
        flash('register_success', 'You are registered and can log in');
          redirect('users/login');
      } else {
        die('something went wrong');
      }

    } else {
      //load view with errors

   $this->view('users/register', $data);
    };



    } else {
          echo $_SERVER['REQUEST_METHOD'];
      //init data
      $data = [
        'name' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      //load view

      $this->view('users/register', $data);
    }
  }

  public function login(){
    $Error = FALSE;
    //check for post
    if($_SERVER['REQUEST_METHOD']=='POST'){
      //Process form
      //sanitize POST Data
            $_POST= filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [

              'email' => trim($_POST['email']),
              'password' => trim($_POST['password']),
              'email_err' => '',
              'password_err' => '',
              'error' => $Error
            ];

            //validate email
              if(empty($data['email'])){
                  $Error=TRUE;
                  $data['email_err']='Please enter email';
              }
              //Validate password
              if(empty($data['password'])){
                  $data['password_err']='Please enter password';
                  $Error=TRUE;
              }


              //Check for user/email
                      if($this->userModel->findUserByEmail($data['email'])){
                        //check and set logged in user
                          $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                          if ($loggedInUser){
                            //create session
                            $this->createUserSession($loggedInUser);
                              redirect('posts/index');
                          }	else {
                            $data['password_err'] = 'Password incorrect';
                            $Error=TRUE;
                            // $this->view('users/login', $data);
                          }

                      } else {
                        $Error=TRUE;
                        $data['email_err']='No User with this Email found';
                        //user not found
                      }



              //make sure errors are empty
              if ($Error==FALSE) {
              //   //Validated
                // var_export($Error);
                // echo gettype($Error);
                die('success');
              } else {
                //load view with errors
             $this->view('users/login', $data);
              };


    }else {
      echo $_SERVER['REQUEST_METHOD'];
      //init data
      $data = [
        'email' => '',
        'password' => '',
        'email_err' => '',
        'password_err' => '',
      ];

      //load view

      $this->view('users/login', $data);
    }
  }


  public function createUserSession($user){
    $_SESSION['user_id'] = $user->id;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_name'] = $user->name;
    redirect('pages/posts');
  }

  public function logout(){
    session_destroy();
    redirect('users/login');
  }




}
