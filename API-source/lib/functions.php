<?

function fetch_json() {
  $_method = strtolower($_SERVER['REQUEST_METHOD']);
  $_json = array();
  switch ($_method) {
    case 'get':
      $_json = $_GET;
    break;
    case 'post':
      $_json = json_decode(file_get_contents('php://input'), true);
      break;
    case 'put':
      $_json = json_decode(file_get_contents('php://input'), true);
    break;
  case 'patch':
    $_json = json_decode(file_get_contents('php://input'), true);
  break;
    case 'delete':
      $_json = json_decode(file_get_contents('php://input'), true);
    break;
  }

  return $_json;
}

function mysql_query($query) {
  global $mysqli;
  $result = $mysqli->query($query) or die(mysqli_error($mysqli));
  return $result;
}

function mysql_fetch_assoc($result) {
  return $result->fetch_assoc();
}

function mysql_num_rows($result) {
  return mysqli_num_rows($result);
}

function mysql_affected_rows() {
  global $mysqli;
  return mysqli_affected_rows($mysqli);
}

function mysql_insert_id() {
  global $mysqli;
  return $mysqli->insert_id;
}

function mysql_sanitize($value) {
  if($value == 'UTC_TIMESTAMP()' || $value == 'NULL' || is_numeric($value)) {
    $value = $value; //leave as it is
  }
  elseif($value == NULL) {
    $value = 'NULL';
  }
  else {
    $value = "'".safe_sql($value)."'";
  }
  return $value;
}

function mysql_insert($table, $columns) {
  if($table == '' || !is_array($columns)) {
    return false;
  }

  //BUILD INSERT
  $column_query = '';
  $value_query = '';

    foreach($columns as $name => $value) {
      $column_query .= ($column_query == '') ? '' : ', ';
      $column_query .= $name;

      $value_query .= ($value_query == '') ? '' : ', ';
      $value_query .= mysql_sanitize($value);
    }

  //BUILD QUERY
  $query = 'INSERT INTO '.$table.' ('.$column_query.') VALUES ('.$value_query.')';
  mysql_query($query);
  return mysql_insert_id();
}

function mysql_query_where_string($column, $value) {
  $operator = '=';
  $value = mysql_sanitize($value);
  if($value == 'NULL') {
    $operator = 'IS';
  }
  $set_string = $column.' '.$operator.' '.$value;
  return $set_string;
}

function mysql_update($table, $columns, $where, $limit = 1) {
  if($table == '' || !is_array($columns) || !is_array($where) || !is_numeric($limit) || count($columns) == 0 || count($where) == 0) {
    return false;
  }
  else {
    //COLUMNS
    $update_query = '';
    foreach($columns as $column => $value) {
      $update_query .= ($update_query == '') ? '' : ', ';
      $update_query .= $column .' = '. mysql_sanitize($value);
    }

    //WHERE CLAUSE
    $where_query = '';
    foreach($where as $column => $value) {
      $where_query .= ($where_query == '') ? '' : ' AND ';
      $where_query .= mysql_query_where_string($column, $value);
    }

    $query = 'UPDATE '.$table.' SET '.$update_query.' WHERE '.$where_query. ' LIMIT '.$limit;
    mysql_query($query);
    return mysql_affected_rows();
  }
}

function mysql_delete($table, $where, $limit = 1) {
  if($table == '' || !is_array($where) || count($where) == 0 || !is_numeric($limit)) {
    return false;
  }
  else {
    //WHERE CLAUSE
    $where_query = '';
    foreach($where as $column => $value) {
      $where_query .= ($where_query == '') ? '' : ' AND ';
      $where_query .= mysql_query_where_string($column, $value);
    }
    $query = 'DELETE FROM '.$table.' WHERE '.$where_query.' LIMIT '.$limit;
    mysql_query($query);
    return mysql_affected_rows();
  }
}

function safe_sql($val)
{
  $val = mysql_prep($val);
  return $val;
}

function mysql_prep($value)
{
    if(get_magic_quotes_gpc()){
        $value = stripslashes($value);
    } else {
        $value = addslashes($value);
    }

    return $value;
}

function response_code($code, $error_symbol = '') {
  if($code == 200) {
    header('HTTP/1.0 200 OK');
  }
  elseif($code == 201) {
    header('HTTP/1.0 201 Created');
  }
  elseif($code == 204) {
    header('HTTP/1.0 204 No Content');
  }
  elseif($code == 400) {
    header('HTTP/1.0 400 Bad Request');
    die();
  }
  elseif($code == 401) {
    header('HTTP/1.0 401 Unauthorized');
    die();
  }
  elseif($code == 404) {
    header('HTTP/1.0 404 Not Found');
    die();
  }
  elseif($code == 405) {
    header('HTTP/1.0 405 Method Not Allowed');
    die();
  }
  elseif($code == 409) {
    header('HTTP/1.0 409 Conflict');
    die();
  }
}

function random_string() {
  $length = 200;
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $characters_length = strlen($characters);
  $random_string = '';
  for ($i = 0; $i < $length; $i++) {
    $random_string .= $characters[mt_rand(0, $characters_length - 1)];
  }
  return $random_string;
}
?>
