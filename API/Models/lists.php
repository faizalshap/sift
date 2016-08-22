<?
  class List {
    public $id;
    public $user_id;
    public $name;

    function __construct($attrs) {
      $this->id = $attrs['id'];
      $this->user_id = $attrs['user_id'];
      $this->name = $attrs['name'];
    }

    public static function list($logged_in_user) {
      $result = mysql_query("SELECT *")
    }
  }
?>
