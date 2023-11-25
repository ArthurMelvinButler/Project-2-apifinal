<?php


define("DBHOST", isset($_ENV["DBHOST"]) ? $_ENV["DBHOST"] : "192.168.182.121");
define("DBUSER", isset($_ENV["DBUSER"]) ? $_ENV["DBUSER"] : "root");
define("DBPWD", isset($_ENV["DBPWD"]) ? $_ENV["DBPWD"] : "Arigato22");
define("DBNAME", isset($_ENV["DBNAME"]) ? $_ENV["DBNAME"] : "belizeonDB");

define("RLHOST", isset($_ENV["RLHOST"]) ? $_ENV["RLHOST"] : "192.168.182.121");
define("RLPORT", isset($_ENV["RLPORT"]) ? $_ENV["RLPORT"] : "8889");
define("RLPWD", isset($_ENV["RLPWD"]) ? $_ENV["RLPWD"] : "Arigato22");
define("RL_MAX", isset($_ENV["RL_MAX"]) ? $_ENV["RL_MAX"] : 5);
define("RL_SECS", isset($_ENV["RL_SECS"]) ? $_ENV["RL_SECS"] : 15); 

define("API_SECRET", isset($_ENV["API_SECRET"]) ? $_ENV["API_SECRET"] : "Host"); //maximum limit 

require_once "./inc/composer/vendor/autoload.php";

use Monolog\Level;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class DBHandler
{
    public $sqlDB = NULL;
    public $log = NULL;
    public $redisDB = NULL; // using redis with some external components

    function __construct()
    {
        // INITIALIZE LOGGER
        $logFilename = date("Y-m-d") . "_activity.log";

        $this->log = new Logger("belizeonDB");
        $handler = new StreamHandler("log/$logFilename");
        $handler->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n"));
        $this->log->pushHandler($handler);

       
        $this->sqlDB = new mysqli(DBHOST, DBUSER, DBPWD, DBNAME);
        if (mysqli_connect_errno()) {
            $this->log->error("Error connecting to mysql. Error:[" . mysqli_connect_error() . "]");
            $this->sqlDB = null;
        }
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


        
        try {
            $this->redisDB = new Redis();
            if (!$this->redisDB->connect(RLHOST, RLPORT)) {
                $this->log->error("Reddis Connection Failed");
                $this->redisDB = null;
            }
            if (!$this->redisDB->auth(RLPWD)) {
                $this->log->error("Redis auth failed");
                $this->redisDB = null;
            }

        } catch (RedisException $e) {
            $this->log->error("Redis Failed.");
        }
    }

    function __destruct()
    {
        if ($this->sqlDB !== null) {
            $this->sqlDB->close();
        }
        if ($this->redisDB !== null) {
            $this->redisDB->close();
        }
    }
}





