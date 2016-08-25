<?
  Route::is('get', 'API/current', function() {
    global $logged_in_user;
    $todos = Todo::fetch_current($logged_in_user);
    echo json_encode($todos);
  });
?>
