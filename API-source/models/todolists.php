<?
  class TodoList {
    public $id;
    public $user_id;
    public $name;
    public $todo_count;

    function __construct($attrs) {
      $this->id = $attrs['id'];
      $this->user_id = $attrs['user_id'];
      $this->name = $attrs['name'];
      $this->todo_count = $attrs['todo_count'];
    }

    public static function fetch($logged_in_user, $list_id) {
      return TodoList::show_all($logged_in_user, $list_id)[0];
    }

    public static function show_all($logged_in_user, $list_id = NULL) {
      $lists = array();
      $query = "SELECT
                tl.id,
                tl.user_id,
                tl.name,
                COUNT(t.id) AS todo_count
                  FROM todolists AS tl
                  LEFT OUTER JOIN todos AS t ON t.todolist_id = tl.id
                    WHERE tl.user_id = ".$logged_in_user->id;
      $query .= ($list_id != NULL) ? " AND tl.id = '".$list_id."'" : ' AND tl.is_hidden = 0';
      $query .= " ORDER BY name ASC";
      $result = mysql_query($query);
      while($row = mysql_fetch_assoc($result)) {
        array_push($lists, new TodoList(array('id' => (int) $row['id'],
                                              'user_id' => (int) $row['user_id'],
                                              'name' => $row['name'],
                                              'todo_count' => (int) $row['todo_count']
                                            )));
      }
      return $lists;
    }

    public static function display_todos($logged_in_user, $todolist_id) {
      if($todolist_id == 'teamgantt') {
        return TeamGantt::fetch_list($logged_in_user, 'today');
      }
      else {
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
                                  WHERE tl.id = '".$todolist_id."' AND tl.user_id = '".$logged_in_user->id."'
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
    }

    public function is_valid() {
      if($this->name == '') {
        return false;
      }

      if($this->user_id == '' || !is_numeric($this->user_id)) {
        return false;
      }

      return true;
    }

    public function save() {
      if($this->id == NULL) {
        $id = mysql_insert('todolists', array('user_id' => $this->user_id,
                                              'name' => $this->name
                                        ));
        $this->id = $id;
      }
      else {
        mysql_update('todolists',
                      array('name' => $this->name),
                      array('id' => $this->id),
                      1);
        return $this;
      }

      return $this;
    }

    public function delete() {
      if($this->id != NULL) {
        //DELETE TODOS
        mysql_delete('todos', array('todolist_id' => $this->id), 10000000);

        //DELETE TODOLIST
        mysql_delete('todolists', array('id' => $this->id), 1);

        return true;
      }
      else {
        return false;
      }
    }
  }
?>
