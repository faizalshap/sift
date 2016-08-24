<?
  Route::is('get', 'teamgantt/today', function() {
    global $logged_in_user;
    $url = 'https://api.teamgantt.com/v1/tasks?today';
    $keys = $logged_in_user->get_api_keys();
    $bearer = 'Bearer dkdp33bvds398dmKKsWW';

    //INITIALIZE
    $tg = curl_init($url);

    //SETUP PARAMS
    curl_setopt($tg, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$bearer,
        'TG-Authorization: '.$bearer,
        'TG-Api-Key: '.$keys['api_key'],
        'TG-User-Token: '.$keys['user_token'],
      ));
    curl_setopt($tg, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($tg, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($tg, CURLOPT_SSL_VERIFYPEER, 0);

    //EXECUTE & RETURN
    $output = curl_exec($tg);
    curl_close($tg);

    //CONVERT & DISPLAY
    $output = json_decode($output);
    $todos = array();
    foreach($output as $task) {
      $todo = new Todo(array( 'name' => $task->name,
                              'percent_complete' => $task->percent_complete
                            ));
      $todo->attach_teamgantt($task);
      array_push($todos, $todo);
    }
    echo json_encode($todos);
  });
?>
