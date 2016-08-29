<?
  //SET HEADERS
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE, PATCH');
  header('Access-Control-Allow-Headers: Origin, S-Api-Key, S-User-Token, Content-Type, X-Requested-With, Authorization, Accept.');
  header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
  header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
  header('Content-Type: application/json');

  $_headers = getallheaders();

  //INCLUDE FUNCTIONS
  $url_root = '../API-source/';
  include $url_root.'../config.php';
  include $url_root.'models/init.php';

  //IF OPTIONS
  if(strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
    response_code(200);
    die();
  }

  if($logged_in_user = User::validate($_headers['S-Api-Key'], $_headers['S-User-Token'])) {
    include $url_root.'routes/init.php';
  }
  else {
    include $url_root.'routes/users.php';
  }

  response_code(404);
?>
