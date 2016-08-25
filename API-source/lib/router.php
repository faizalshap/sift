<?
  class Route {
    public static function is($method, $query, $return_function) {
      $route = new Route;
      $route->method = $method;
      $route->query = $query;

      if($route->method == $route->method() && $route->check_route()) {
        call_user_func_array($return_function,  $route->params); //RUN THE FUNCTION PASSED IN
        die(); //KILL THE PAGE - SO NOTHING DOUBLE LOADS
      }
      else {
        return false;
      }
    }

    public function method() {
      return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function url_parts($url_string) {
      $url = explode('/', $url_string);
      if($url[count($url)-1] == '') {
        unset($url[count($url)-1]);
      }
      return $url;
    }

    public function check_route() {
      //DEFINE URLS
      $check_url = $this->url_parts($this->query); //GET PARTS OF URL WE WILL COMPARE AGAINST
      $current_url = $this->url_parts(substr(explode('?',$_SERVER['REQUEST_URI'])[0],1)); //GET URL OF OUR CURRENT URL

      //START BUILDING OUR RETURN OBJECT
      $ret = array();

      //IF URL DIRECTORY COUNT DOESN'T MATCH - FAIL
      if(count($check_url) != count($current_url)) {
        return false;
      }

      //LOOP THROUGH QUERY DIRs TO SEE IF IT'S A MATCH
      for($i = 0; $i < count($current_url); $i++) {
        if(stristr($check_url[$i],'{$')) {
          //IF $check_url PART IS A VARIABLE {$var_name} - ADD IT TO OUR RETURN OBJECT
          $param_name = str_replace('{$','',str_replace('}','',$check_url[$i]));

          //CLEANUP IF IT'S A NUMBER
          if(is_numeric($current_url[$i])) {
            $current_url[$i] = (int) $current_url[$i];
          }

          //DEFINE IT
          $ret[$param_name] = $current_url[$i];
        }
        elseif($check_url[$i] != $current_url[$i]) {
          return false; //IF QUERY DIRS DON'T MATCH - FAIL
        }
      }

      //RETURN OUR OBJECT
      $this->params = (array) $ret;
      return true;
    }
  }
?>
