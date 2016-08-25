<?
/* GET ALL TODOLISTS */
Route::is('get', 'API/todolists', function() {
  global $logged_in_user;
  if($todolists = TodoList::show_all($logged_in_user)) {
    echo json_encode($todolists);
  }
  else {
    error_code(404);
  }
});

/* CREATE NEW TODOLIST */
Route::is('post', 'API/todolists', function() {
  global $logged_in_user;
  $json = fetch_json();

  $todolist = new TodoList(array( 'user_id' => $logged_in_user->id,
                                  'name' => $json['name']));

  if(!$todolist->is_valid()) {
    response_code(400);
  }

  $todolist->save();
  echo json_encode($todolist);
});

/* RENAME TODOLIST */
Route::is('put', 'API/todolists/{$todolist_id}', function($todolist_id) {
  global $logged_in_user;
  $json = fetch_json();

  $todolist = TodoList::fetch($logged_in_user, $todolist_id);
  if(!$todolist) {
    response_code(404);
  }

  if(!isset($json['name'])) {
    response_code(400);
  }

  $todolist->name = $json['name'];
  if(!$todolist->is_valid()) {
    response_code(400);
  }

  $todolist->save();
  echo json_encode($todolist);
});

/* DELETE TODOLIST */
Route::is('delete', 'API/todolists/{$todolist_id}', function($todolist_id) {
  global $logged_in_user;

  //MAKE SURE WE CAN ACCESS TODOLIST
  $todolist = TodoList::fetch($logged_in_user, $todolist_id);
  if(!$todolist) {
    response_code(404);
  }

  //DELETE IT
  if(!$todolist->delete()) {
    response_code(404);
  }

  //RETURN
  response_code(204);
});

/* GET TODOLIST DATA */
Route::is('get', 'API/todolists/{$todolist_id}', function($todolist_id) {
  global $logged_in_user;
  if($todolist = TodoList::fetch($logged_in_user,$todolist_id)) {
    echo json_encode($todolist);
  }
  else {
    response_code(404);
  }
});

/* GET ALL TODOS IN A TODOLIST */
Route::is('get', 'API/todolists/{$todolist_id}/todos', function($todolist_id) {
  global $logged_in_user;
  if($todos = TodoList::display_todos($logged_in_user,$todolist_id)) {
    echo json_encode($todos);
  }
  else {
    error_code(404);
  }
});
?>
