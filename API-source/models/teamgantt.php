<?
  class TeamGanttTodo extends Todo {
    public function attach_teamgantt($task) {
      $this->teamgantt = (object) array();
      $this->teamgantt->id = (integer) $task->id;
      $this->teamgantt->group_name = $task->group_name;
      $this->teamgantt->project_name = $task->project_name;
    }

    public function fetch_teamgantt_todolist_id($logged_in_user) {
      $teamgantt_todolist_name = 'TeamGantt Todos';
      $result = mysql_query("SELECT
                              id
                                FROM todolists
                                WHERE user_id = '".$logged_in_user->id."' AND is_hidden = 1 AND `name` = '".$teamgantt_todolist_name."'");
      if(mysql_num_rows($result) == 0) {
        mysql_insert('todolists', array('user_id' => $logged_in_user->id,
                                        'name' => $teamgantt_todolist_name,
                                        'is_hidden' => 1));
        return mysql_insert_id();
      }
      else {
        while($row = mysql_fetch_assoc($result)) {
          return $row['id'];
        }
      }
    }

    public function does_exist($logged_in_user) {
      if(isset($this->teamgantt_id)) {
        $result = mysql_query("SELECT
                                t.id,
                                t.created_at
                                  FROM todos AS t
                                  JOIN todolists AS tl ON tl.id = t.todolist_id
                                  WHERE t.teamgantt_id = '".$this->teamgantt_id."' AND tl.user_id = '".$logged_in_user->id."'");
        while($row = mysql_fetch_assoc($result)) {
          $this->id = $row['id'];
          $this->created_at = $row['created_at'];
        }
      }
    }

    public function run_patch($logged_in_user) {
      //RUN PATCH TO TEAMGANTT
      $url = 'https://api.teamgantt.com/v1/tasks/'.$this->teamgantt_id;
      $data = array('name' => $this->name,
                    'percent_complete' => $this->percent_complete
                );
      echo API::run($logged_in_user, 'patch', $url, $data);
    }
  }

  class API {
    public static function run($logged_in_user, $method, $url, $data = array()) {
      //AUTH
      $keys = $logged_in_user->get_api_keys();
      $bearer = 'Bearer dkdp33bvds398dmKKsWW';

      //INITIALIZE
      $tg = curl_init($url);
      $headers = array(
          'Authorization: '.$bearer,
          'TG-Authorization: '.$bearer,
          'TG-Api-Key: '.$keys['api_key'],
          'TG-User-Token: '.$keys['user_token']
        );

      //SETUP PARAMS
      curl_setopt($tg, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($tg, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($tg, CURLOPT_SSL_VERIFYPEER, 0);

      if($method == 'patch') {
        curl_setopt($tg, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($tg, CURLOPT_POSTFIELDS, json_encode($data));
      }

      //EXECUTE & RETURN
      $output = curl_exec($tg);

      curl_close($tg);
      return json_decode($output);
    }
  }
?>
