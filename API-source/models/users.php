<?
  class User {
    public $id;
    public $username;
    private $password;

    function __construct($attrs) {
      $this->id = $attrs['id'];
      $this->username = $attrs['username'];
      $this->password = $attrs['password'];
    }

    public function fetch_password() {
      return $this->password;
    }

    public static function create($attrs) {
      if(isset($attrs['username']) && isset($attrs['password'])) {
        //MAKE SURE IT DOESN'T EXIST
        $check = mysql_query("SELECT id FROM users WHERE username = '".safe_sql($attrs['username'])."' LIMIT 1");
        if(mysql_num_rows($check) > 0) {
          return false;
        }

        //ADD NEW USER
        $new_user = mysql_insert('users', array('username' => $attrs['username'],
                                                'password' => md5($attrs['password'])));

        return new User(array('id' => $new_user,
                              'username' => $attrs['username'],
                              'password' => $attrs['password']
                            ));
      }
      else {
        return false;
      }
    }

    public static function validate($key, $token) {
      $result = mysql_query("SELECT
                                u.id,
                                u.username
                                  FROM logins AS l
                                  JOIN users AS u ON u.id = l.user_id
                                    WHERE l.user_key = '".$key."' AND l.user_token = '".$token."'");
      if(mysql_num_rows($result) == 1) {
        while($row = mysql_fetch_assoc($result)) {
          return new User(array('id' => $row['id'],
                                'username' => $row['username'],
                                'password' => NULL));
        }
      }
      else {
        return false;
      }
    }

    public function login($user, $pass) {
      $result = mysql_query("SELECT
                              id
                                FROM users
                                WHERE username = '".safe_sql($user)."' AND password = '".safe_sql(md5($pass))."'");
      if(mysql_num_rows($result) == 1) {
        while($row = mysql_fetch_assoc($result)) {
          //BUILD VARIABLES
          $user_id = $row['id'];
          $key = random_string();
          $token = random_string();
          $login_array = array( 'user_id' => (int) $user_id,
                                'user_key' => $key,
                                'user_token' => $token);

          //LOGIN
          $do_login = mysql_insert('logins',$login_array);
          if(!$do_login) {
            return false;
          }

          return $login_array;
        }
      }
      else {
        return false;
      }
    }
  }
?>
