<?
  Route::is('get', 'API/todolists/teamgantt/todos', function() {
    global $logged_in_user;
    $teamgantt_tasks = API::run($logged_in_user, 'GET', 'https://api.teamgantt.com/v1/tasks?today');

    //CONVERT & DISPLAY
    $todos = array();
    foreach($teamgantt_tasks as $task) {
      $todo = new TeamGanttTodo;
      $todo->attach_teamgantt($task);
      array_push($todos, $todo);
    }
    echo json_encode($todos);
  });

  Route::is('post', 'API/todolists/teamgantt/todos', function() {
    global $logged_in_user;
    $json = fetch_json();

    //SETUP NEW TODO
    $todo = new TeamGanttTodo(array('name' => $json['name'],
                                    'percent_complete' => $json['percent_complete'] ?? 0,
                                    'is_big_rock' => false,
                                    'is_current' => true,
                                    'created_at' => gmdate('Y-m-d H:i:s'),
                                    'updated_at' => gmdate('Y-m-d H:i:s')
                                ));
    //APPEND TEAMGANTT INFO
    $todo->teamgantt_id = (int) $json['id'];
    $todo->todolist_id = $todo->fetch_teamgantt_todolist_id($logged_in_user);
    $todo->does_exist($logged_in_user);
    $todo->fetch_teamgantt_meta($logged_in_user);

    //VERIFY
    if(!$todo->is_valid()) {
      response_code(400);
    }

    //SAVE & RETURN
    $todo->save();
    echo json_encode($todo);
  });
?>
