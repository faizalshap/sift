<?
/* GET TODO */
Route::is('get', 'API/todolists/{$todolist_id}/todos/{$todo_id}', function($todolist_id, $todo_id) {
  global $logged_in_user;
  if($todo = Todo::fetch($logged_in_user, $todo_id)) {
    echo json_encode($todo);
  }
  else {
    response_code(404);
  }
});

/* CREATE NEW TODO */
Route::is('post', 'API/todolists/{$todolist_id}/todos', function($todolist_id) {
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
                          'is_current' => (bool) $json['is_current'],
                          'created_at' => gmdate('Y-m-d H:i:s'),
                          'updated_at' => gmdate('Y-m-d H:i:s')
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
Route::is('put', 'API/todolists/{$todolist_id}/todos/{$todo_id}', function($todolist_id, $todo_id) {
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
  $todo = ($original_todo->teamgantt_id == NULL) ? new Todo($json) : new TeamGanttTodo($json);
  $todo->updated_at = gmdate('Y-m-d H:i:s');
  $todo->teamgantt_id = $original_todo->teamgantt_id;

  //MAKE SURE IT'S VALID
  if(!$todo->is_valid()) {
    response_code(400);
  }

  //SAVE & RETURN
  $todo->save();
  echo json_encode($todo);

  //IF TEAMGANTT - WE NEED TO UPDATE IT
  if($todo->is_teamgantt) {
    $todo->run_patch($logged_in_user);
  }
});

/* DELETE TODO */
Route::is('delete', 'API/todolists/{$todolist_id}/todos/{$todo_id}', function($todolist_id, $todo_id) {
  global $logged_in_user;
  //MAKE SURE WE CAN EDIT THE TODO
  $todo = Todo::fetch($logged_in_user, $todo_id);
  if(!$todo) {
    response_code(404);
  }

  $todo->delete();
});
?>
