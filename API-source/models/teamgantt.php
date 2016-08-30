<?
  class TeamGantt {
    public function fetch_teamgantt_todolist_id($logged_in_user) {
      $teamgantt_todolist_name = 'My TeamGantt Tasks';
      $result = mysql_query("SELECT
                              id
                                FROM todolists
                                WHERE user_id = '".$logged_in_user->id."' AND is_hidden = 1 AND `name` = '".$teamgantt_todolist_name."'");
      if(mysql_num_rows($result) == 0) {
        mysql_insert('todolists', array('user_id' => $logged_in_user->id,
                                        'name' => $teamgantt_todolist_name,
                                        'created_at' => gmdate("Y-m-d H:i:s"),
                                        'updated_at' => gmdate("Y-m-d H:i:s"),
                                        'is_hidden' => 1));
        return mysql_insert_id();
      }
      else {
        while($row = mysql_fetch_assoc($result)) {
          return $row['id'];
        }
      }
    }

    public static function synced_tasks($logged_in_user) {
      $todos = array();
      $result = mysql_query("SELECT
                              t.id,
                              t.todolist_id,
                              t.name,
                              t.percent_complete,
                              t.is_big_rock,
                              t.is_current,
                              t.created_at,
                              t.updated_at,
                              t.teamgantt_id,
                              t.teamgantt_meta
                                FROM todolists AS tl
                                JOIN todos AS t ON t.todolist_id = tl.id
                                WHERE tl.user_id = '".$logged_in_user->id."' AND t.teamgantt_id != ''
                                ORDER BY t.created_at ASC");
      while($row = mysql_fetch_assoc($result)) {
        array_push($todos, new Todo(array('id' => (int) $row['id'],
                                          'todolist_id' => (int) $row['todolist_id'],
                                          'name' => $row['name'],
                                          'percent_complete' => (int) $row['percent_complete'],
                                          'is_big_rock' => (bool) $row['is_big_rock'],
                                          'is_current' => (bool) $row['is_current'],
                                          'created_at' => $row['created_at'],
                                          'updated_at' => $row['updated_at'],
                                          'teamgantt_id' => $row['teamgantt_id'],
                                          'teamgantt_meta' => $row['teamgantt_meta']
                                      )));
      }
      return $todos;
    }

    public static function sync($logged_in_user) {
      if($keys = $logged_in_user->get_api_keys()) {
        //SIFT TEAMGANTT LIST ID
        $teamgantt_todolist_id = TeamGantt::fetch_teamgantt_todolist_id($logged_in_user);

        //PULL TODOS FROM TEAMGANTT & LINK THEIR IDS BACK TO TODO
        $todos = TeamGantt::synced_tasks($logged_in_user);
        $teamgantt_ids = array(); //array used to match teamgantt ids to todo
        foreach($todos as $t => $todo) {
          $teamgantt_ids[$todo->teamgantt_id] = $t;
        }

        //PULL TEAMGANTT TASKS
        $url = 'https://api.teamgantt.com/v1/tasks?today';
        $teamgantt_tasks = API::run($logged_in_user, 'GET', $url);

        //IF NO ERROR - LOOP THROUGH RESULTS
        if(!$teamgantt_tasks->error) {

          foreach($teamgantt_tasks as $task) {
            if(!isset($teamgantt_ids[$task->id])) {
              //BUILD RESOURCE ARRAY
              $resource_array = array();
              foreach($task->resources as $resource) {
                $resource_data = (object) array('name' => $resource->name,
                                                'pic' => $resource->pic);
                array_push($resource_array, $resource_data);
              }

              //CREATE NEW TODO
              $todo = new Todo( array('todolist_id' => $teamgantt_todolist_id,
                                      'name' => $task->name,
                                      'percent_complete' => $task->percent_complete,
                                      'created_at' => gmdate("Y-m-d H:i:s"),
                                      'updated_at' => gmdate("Y-m-d H:i:s"),
                                      'teamgantt_id' => $task->id,
                                      'teamgantt_meta' => json_encode(array('project_name' => $task->project_name,
                                                                            'group_name' => $task->group_name,
                                                                            'end_date' => $task->end_date,
                                                                            'resources' => $resource_array))
                            ));

              //VALIDATE & SAVE
              if($todo->is_valid()) {
                $todo->save();
              }
            }
            else {

              //PULL CORRECT TODO
              $todo = $todos[$teamgantt_ids[$task->id]];
              $did_update = false;

              //UPDATE NAME
              if($todo->name != $task->name) {
                $todo->name = $task->name;
                $did_update = true;
              }

              //UPDATE PERCENT COMPLETE
              if($todo->percent_complete != $task->percent_complete) {
                $todo->percent_complete = $task->percent_complete;
                $did_update = true;
              }

              //IF WE UPDATED IT - SAVE THE CHANGES
              if($did_update) {
                $todo->updated_at = gmdate("Y-m-d H:i:s");
                if($todo->is_valid()) {
                  $todo->save();
                }
              }
            }
          }
        }
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
