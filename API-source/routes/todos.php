<?
/* GET TODO */
Route::is('get', 'todolists/{$todolist_id}/todos/{$todo_id}', function($todolist_id, $todo_id) {
  global $logged_in_user;
  if($todo = Todo::fetch($logged_in_user, $todo_id)) {
    echo json_encode($todo);
  }
  else {
    response_code(404);
  }
});

/* CREATE NEW TODO */
Route::is('post', 'todolists/{$todolist_id}/todos', function($todolist_id) {
  global $logged_in_user;
  $json = fetch_json();

  //MAKE SURE TODOLIST EXISTS
  $todolist = TodoList::fetch($logged_in_user, $todolist_id);
  if(!$todolist) {
    response_code(404);
  }

  //BUILD TODO
  $todo = new Todo(array( 'todolist_id' => $todolist_id,
                          'name' => $json['name'],
                          'percent_complete' => $json['percent_complete'],
                          'is_big_rock' => (bool) $json['is_big_rock'],
                          'is_current' => (bool) $json['is_current']
                      ));

  //VALIDATE TODO
  if(!$todo->is_valid()) {
    response_code(400);
  }

  //SAVE
  $todo->save();
  echo json_encode($todo);
});

/* UPDATE TODO */
Route::is('put', 'todolists/{$todolist_id}/todos/{$todo_id}', function($todolist_id, $todo_id) {
  global $logged_in_user;
  $json = fetch_json();
  $json['id'] = $todo_id;
  $json['todolist_id'] = $json['todolist_id'] ?? $todolist_id;

  //MAKE SURE WE CAN EDIT THE TODO
  $original_todo = Todo::fetch($logged_in_user, $todo_id);
  if(!$original_todo || $original_todo->todolist_id != $todolist_id) {
    response_code(404);
  }

  //MAKE SURE WE CAN POST INTO NEW TODOLIST
  $todolist = TodoList::fetch($logged_in_user, $json['todolist_id']);
  if(!$todolist) {
    response_code(404);
  }

  //SETUP NEW TODO
  $todo = new Todo($json);

  //MAKE SURE IT'S VALID
  if(!$todo->is_valid()) {
    response_code(400);
  }

  //SAVE & RETURN
  $todo->save();
  echo json_encode($todo);
});

/* DELETE TODO */
Route::is('delete', 'todolists/{$todolist_id}/todos/{$todo_id}', function($todolist_id, $todo_id) {
  global $logged_in_user;
  //MAKE SURE WE CAN EDIT THE TODO
  $todo = Todo::fetch($logged_in_user, $todo_id);
  if(!$todo) {
    response_code(404);
  }

  $todo->delete();
});
?>
