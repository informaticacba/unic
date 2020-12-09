<?php
/**
* Models
* Model Class is handle all the database settings and database transactions.
*
* @package : Model
* @category : System
* @author : Unic Framework
* @link : https://github.com/unicframework/unic
*/

defined('SYSPATH') OR exit('No direct access allowed');

//Live database connections
$live_connections = array();

class Models {

  /**
  * Connect
  * Initialize database connection manually.
  *
  * @param array $db_name
  * @return void
  */
  protected function connect(...$db_name) {
    global $db, $live_connections;

    //Parse dbname
    if(!is_array($db_name)) {
      $db_name = array($db_name);
    }

    foreach($db_name as $name) {
      //Check db connection already exists or not
      if(isset($live_connections[$name]) && $live_connections[$name] != false) {
        //Already connected
        $this->$name = $live_connections[$name];
      } else {
        //Check db settings
        if(isset($db) && is_array($db)) {
          $db_setting_array = $db;
        } else {
          exit('Invalid database setting');
        }

        //Parse database settings
        $db_setting = $this->parse_db($db_setting_array, $db_name);

        //Initialize database connection
        $this->load_driver($db_setting, $db_name);
      }
    }
  }

  /**
  * Load Driver
  * Load database driver.
  *
  * @param array $db_setting
  * @param array $connect
  * @return mixed
  */
  private function load_driver(array $db_setting, array $connect) {
    global $live_connections;
    //Check db driver exists or not
    foreach($connect as $name) {
      //Check database setting exists or not
      if(array_key_exists($name, $db_setting)) {
        $driver_name = strtolower($db_setting[$name]['driver']);
        if(file_exists(SYSPATH.'/database/'.$driver_name.'_driver.php')) {
          require_once(SYSPATH.'/database/'.$driver_name.'_driver.php');
          $driver = $driver_name.'_db_driver';
          if(class_exists($driver)) {
            $live_connections[$name] = new $driver($db_setting, $name);
            $this->$name = &$live_connections[$name];
          } else {
             exit("'".$db_setting[$name]['driver']."' : Database driver not found");
          }
        } else {
          exit("'".$db_setting[$name]['driver']."' : Database driver not found");
        }
      } else {
        exit("'".$name."' : Database setting not found");
      }
    }
  }

  /**
  * Parse DB
  * Parse database connection settings.
  *
  * @param array $db
  * @param array $connect
  * @return array
  */
  private function parse_db(array $db, array $connect) : array {
    //Set db_config default data type.
    $db_config = array();
    foreach($connect as $name) {
      //Check database setting exists or not
      if(array_key_exists($name, $db)) {
        if(isset($db[$name]['dsn']) && $db[$name]['dsn'] != NULL) {
          $db_config[$name]['dsn'] = $db[$name]['dsn'];
        } else {
          $db_config[$name]['dsn'] = NULL;
        }
        if(isset($db[$name]['hostname']) && $db[$name]['hostname'] != NULL) {
          $db_config[$name]['hostname'] = $db[$name]['hostname'];
        } else {
          $db_config[$name]['hostname'] = NULL;
        }
        if(isset($db[$name]['port']) && $db[$name]['port'] != NULL) {
          $db_config[$name]['port'] = $db[$name]['port'];
        } else {
          $db_config[$name]['port'] = NULL;
        }
        if(isset($db[$name]['username']) && $db[$name]['username'] != NULL) {
          $db_config[$name]['username'] = $db[$name]['username'];
        } else {
          $db_config[$name]['username'] = NULL;
        }
        if(isset($db[$name]['password']) && $db[$name]['password'] != NULL) {
          $db_config[$name]['password'] = $db[$name]['password'];
        } else {
          $db_config[$name]['password'] = NULL;
        }
        if(isset($db[$name]['database']) && $db[$name]['database'] != NULL) {
          $db_config[$name]['database'] = $db[$name]['database'];
        } else {
          $db_config[$name]['database'] = NULL;
        }
        if(isset($db[$name]['driver']) && $db[$name]['driver'] != NULL) {
          $db_config[$name]['driver'] = $db[$name]['driver'];
        } else {
          $db_config[$name]['driver'] = NULL;
        }
        if(isset($db[$name]['char_set']) && $db[$name]['char_set'] != NULL) {
          $db_config[$name]['char_set'] = $db[$name]['char_set'];
        } else {
          $db_config[$name]['char_set'] = NULL;
        }
      } else {
        exit("'".$name."' : Database setting not found");
      }
    }
    return $db_config;
  }
}
