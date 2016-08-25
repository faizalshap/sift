<?

/* NEW USER */
Route::is('post', 'API/users', function() {
  $json = fetch_json();

  //VALIDATE ENTERED PARAMS
  if(isset($json['username']) && isset($json['password']) && isset($json['password_confirm'])) {
    if($json['password'] != $json['password_confirm']) {
      response_code(400);
    }

    //CREATE USER
    $user = User::create(array('username' => $json['username'], 'password' => $json['password']));

    //LOGIN USER
    $login = User::login($user->username, $json['password']);
    if(!$login) {
      response_code(401);
    }

    //CREATE FIRST TODOLIST
    $todolist = new TodoList(array( 'user_id' => $user->id,
                                    'name' => 'Inbox'));
    $todolist->save();

    //SETUP DEFAULT TODOS
    $default_todos = array( 'This is the first todo.',
                            'This is the second todo.',
                            'This is the third todo.');
    foreach($default_todos as $name) {
      $todo = new Todo(array( 'todolist_id' => $todolist->id,
                              'percent_complete' => 0,
                              'name' => $name));
      $todo->save();
    }

    //RETURN LOGIN CREDS
    echo json_encode($login);
  }
});

/* LOGIN */
Route::is('post', 'API/login', function() {
  $json = fetch_json();

  $login = User::login($json['username'], $json['password']);
  if(!$login) {
    response_code(401);
  }

  echo json_encode($login);
});

?>
