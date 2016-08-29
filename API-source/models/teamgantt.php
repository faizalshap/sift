<?
  class TeamGantt {
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

    public static function fetch_list($logged_in_user, $query) {
      if($query == 'today') {
        $teamgantt_tasks = API::run($logged_in_user, 'GET', 'https://api.teamgantt.com/v1/tasks?today');
        if(!$teamgantt_tasks->error) {
          //CONVERT & DISPLAY
          $todos = array();
          foreach($teamgantt_tasks as $task) {
            //SETUP RESOURCES IN ADVANCE
            if($task->percent_complete != 100) {
              $resource_array = array();
              foreach($task->resources as $resource) {
                $resource_data = (object) array('name' => $resource->name,
                                                'pic' => $resource->pic);
                array_push($resource_array, $resource_data);
              }

              $todo = new Todo( array('todolist_id' => 'teamgantt',
                                      'name' => $task->name,
                                      'percent_complete' => $task->percent_complete,
                                      'teamgantt_id' => $task->id,
                                      'teamgantt_meta' => (object) array('project_name' => $task->project_name,
                                                                          'group_name' => $task->group_name,
                                                                          'end_date' => $task->end_date,
                                                                          'resources' => $resource_array)
                            ));
              array_push($todos, $todo);
            }
          }
          return $todos;
        }
        else {
          return false;
        }
      }
      else {
        return false;
      }
    }

    public function patch_task($logged_in_user, $todo) {
      //RUN PATCH TO TEAMGANTT
      if($todo->teamgantt_id != NULL) {
        $url = 'https://api.teamgantt.com/v1/tasks/'.$todo->teamgantt_id;
        $data = array('name' => $todo->name,
                      'percent_complete' => $todo->percent_complete
                  );
        API::run($logged_in_user, 'patch', $url, $data);
      }
    }
  }

  class API {
    public static function run($logged_in_user, $method, $url, $data = array()) {
      //AUTH
      if($keys = $logged_in_user->get_api_keys()) {
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
      else {
        return false;
      }
    }
  }
?>
