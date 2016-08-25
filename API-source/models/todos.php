<?
  class Todo {
    public $id;
    public $todolist_id;
    public $name;
    public $percent_complete;
    public $is_big_rock;
    public $is_current;
    public $created_at;
    public $updated_at;
    public $teamgantt_id;

    function __construct($attrs) {
      $this->id = $attrs['id'];
      $this->todolist_id = $attrs['todolist_id'];
      $this->name = $attrs['name'];
      $this->percent_complete = $attrs['percent_complete'];
      $this->is_big_rock = $attrs['is_big_rock'];
      $this->is_current = $attrs['is_current'];
      $this->created_at = $attrs['created_at'];
      $this->updated_at = $attrs['updated_at'];
      $this->teamgantt_id = $attrs['teamgantt_id'];
    }

    public static function fetch($logged_in_user, $id) {
      $todo = array();
      $result = mysql_query("SELECT
                              t.id,
                              t.todolist_id,
                              t.name,
                              t.percent_complete,
                              t.is_big_rock,
                              t.is_current,
                              t.created_at,
                              t.updated_at,
                              t.teamgantt_id
                                FROM todos AS t
                                JOIN todolists AS tl ON tl.id = t.todolist_id
                                WHERE t.id = '".$id."' AND tl.user_id = '".$logged_in_user->id."'");
      while($row = mysql_fetch_assoc($result)) {
        $todo = new Todo(array( 'id' => (int) $row['id'],
                                'todolist_id' => (int) $row['todolist_id'],
                                'name' => $row['name'],
                                'percent_complete' => (int) $row['percent_complete'],
                                'is_big_rock' => (bool) $row['is_big_rock'],
                                'is_current' => (bool) $row['is_current'],
                                'created_at' => $row['created_at'],
                                'updated_at' => $row['updated_at'],
                                'teamgantt_id' => $row['teamgantt_id']
                            ));
      }

      return $todo;
    }

    public static function fetch_current($logged_in_user) {
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
                              t.teamgantt_id
                                FROM todolists AS tl
                                JOIN todos AS t ON t.todolist_id = tl.id
                                WHERE tl.user_id = '".$logged_in_user->id."' AND t.is_current = 1
                                ORDER BY is_big_rock DESC, t.created_at");
      while($row = mysql_fetch_assoc($result)) {
        array_push($todos, new Todo(array(  'id' => (int) $row['id'],
                                            'todolist_id' => (int) $row['todolist_id'],
                                            'name' => $row['name'],
                                            'percent_complete' => (int) $row['percent_complete'],
                                            'is_big_rock' => (bool) $row['is_big_rock'],
                                            'is_current' => (bool) $row['is_current'],
                                            'created_at' => $row['created_at'],
                                            'updated_at' => $row['updated_at'],
                                            'teamgantt_id' => $row['teamgantt_id']
                                        )));
      }

      return $todos;
    }

    public function can_edit($logged_in_user) {
      if($this->original_todolists_id != $this->todolist_id) {
        $new_list = TodoList::fetch($logged_in_user, $this->todolist_id);
        if(!$new_list) {
          return false;
        }
      }

      $result = mysql_query("SELECT
                              t.id
                                FROM todos AS t
                                JOIN todolists AS tl ON tl.id = t.todolist_id
                                  WHERE t.id = '".safe_sql($this->id)."'
                                  AND t.todolist_id = '".safe_sql($this->original_todolist_id)."'
                                  AND tl.user_id = '".safe_sql($logged_in_user->id)."'");
      if(mysql_num_rows($result) == 1) {
        return true;
      }
      else {
        return false;
      }
    }

    public function is_valid() {
      //NAME
      if($this->name == '') {
        return false;
      }

      //PERCENT COMPLETE
      if($this->percent_complete == NULL) {
        $this->percent_complete = 0;
      }
      else {
        if($this->percent_complete > 100) {
          $this->percent_complete = 100;
        }
        elseif($this->percent_complete < 0) {
          $this->percent_complete = 0;
        }
        else {
          $this->percent_complete = (int) $this->percent_complete;
        }
      }

      return true;
    }

    public function save() {
      if($this->id == NULL) {
        $save_array = array('todolist_id' => $this->todolist_id,
                            'name' => $this->name,
                            'percent_complete' => $this->percent_complete,
                            'is_big_rock' => (integer) $this->is_big_rock,
                            'is_current' => (integer) $this->is_current,
                            'created_at' => $this->created_at,
                            'updated_at' => $this->updated_at
                        );
        if(isset($this->teamgantt_id)) {
          $save_array['teamgantt_id'] = $this->teamgantt_id;
        }

        $id = mysql_insert('todos', $save_array);
        $this->id = $id;
      }
      else {
        mysql_update('todos',
                      array('todolist_id' => $this->todolist_id,
                            'name' => $this->name,
                            'percent_complete' => $this->percent_complete,
                            'is_big_rock' => (integer) $this->is_big_rock,
                            'is_current' => (integer) $this->is_current,
                            'updated_at' => $this->updated_at
                        ),
                      array('id' => $this->id),
                      1);
      }

      return $this;
    }

    public function delete() {
      if(is_numeric($this->id)) {
        mysql_delete('todos', array('id' => $this->id), 1);
        return true;
      }
      else {
        return false;
      }
    }
  }
?>
