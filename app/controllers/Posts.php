<?php

//the Core Controller looks here if url-matching methods exist, eg. "/show/" "/edit"

  class Posts extends Controller {

    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login'); //leite um wenn der man nicht eingeloogt ist
      }

      $this->postModel = $this->model('Post'); //hole die Daten aus Datenbank
      $this->userModel = $this->model('User');
    } //constructor ends here


    public function index(){  //hier werden die Daten aus der Datenbank geholt
      //Get posts

      $posts= $this->postModel->getPosts();

      $data = [
        'posts' => $posts,

      ];

      $this->view('posts/index', $data);  //hier wird das ganze dann schlussendlich für den Browser zusammengestellt
    }

    public function add(){
        $error=false;
      if($_SERVER['REQUEST_METHOD']== 'POST'){
        //Sanitize POST Array
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data= [
          'title' => trim($_POST['title']),
          'body' => trim($_POST['body']),
          'user_id' => $_SESSION['user_id'],
          'title_err' => '',
          'body_err' => '',
        ];
        //validate the title
        if(empty($data['title'])){
          $data['title_err']= 'Please enter title';
          $error=true;
        }
        if(empty($data['body'])){
          $data['body_err']= 'Please enter body';
          $error=true;
        }

        //make sure no errors

        if(!$error){
          //validated
          if($this->postModel->addPost($data)){
            flash('post_message', 'Post Added');
            redirect('posts');
          } else {
            die('Something went wrong');
          }
        } else {
          //load view with errors
          $this->view('posts/add', $data);
        }

      } else {


      $data = [
        'title' => '',
        'body' => ''
      ];

      $this->view('posts/add', $data);
    }

  }

  public function edit($id){
      $error=false;
    if($_SERVER['REQUEST_METHOD']== 'POST'){
      //Sanitize POST Array
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data= [
        'id' => $id,
        'title' => trim($_POST['title']),
        'body' => trim($_POST['body']),
        'user_id' => $_SESSION['user_id'],
        'title_err' => '',
        'body_err' => '',
      ];
      //validate the title
      if(empty($data['title'])){
        $data['title_err']= 'Please enter title';
        $error=true;
      }
      if(empty($data['body'])){
        $data['body_err']= 'Please enter body';
        $error=true;
      }

      //make sure no errors

      if(!$error){
        //validated
        if($this->postModel->updatePost($data)){
          flash('post_message', 'Post Updated');
          redirect('posts');
        } else {
          die('Something went wrong');
        }
      } else {
        //load view with errors
        $this->view('posts/edit', $data);
      }

    } else {

//get existing post from the model
      $post = $this->postModel->getPostById($id);


  //Check for owner
      if($post->user_id != $_SESSION['user_id']){
        redirect('posts'); //if the logged in user is not the owner, than redirect to same page
      };

      $data = [
        'id' => $id,
        'title' => $post->title,
        'body' => $post->body,
      ];


    $this->view('posts/edit', $data);
  }

}


  public function show($id){

    $post = $this->postModel->getPostById($id);
    $user = $this->userModel->getUserById($post->user_id);
    $data = [
      'post' => $post,
      'id' => $id,
      'user' => $user,
    ];
    $this->view('posts/show', $data);
}
    public function delete($id){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        //get existing post from the model
              $post = $this->postModel->getPostById($id);


          //Check for owner
              if($post->user_id != $_SESSION['user_id']){
                redirect('posts'); //if the logged in user is not the owner, than redirect to same page
              };

        if($this->postModel->deletePost($id)){
          flash('post_message', 'Post Deleted');
          redirect('posts');
        } else {
          die('Something went wrong!');
        }
      } else {
      //  redirect('posts');
          die('Something went wrong!');
        }
    }
  }
