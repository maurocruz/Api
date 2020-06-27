<?php

namespace Fwc\Api\Server;

class PDOConnect 
{
    private static $PDOConnect;
    
    private static $driver;
    private static $host;
    private static $dbname;
    
    private static $userPublic;
    private static $passwordPublic;
    
    private static $usernameAdmin;
    private static $emailAdmin;
    private static $passwordAdmin;

    public static function connect($driver, $host, $dbname, $username, $password, $options = []) 
    {
        self::$driver = $driver;
        self::$host = $host;
        self::$dbname = $dbname;
        
        if(self::$PDOConnect == null) {
            $default_options = [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => true
            ];

            $options = array_replace($default_options, $options);
                   
            $dsn = $driver . ":host=" . $host . ";dbname=" . $dbname;
            
            try {
                $PDOConnect = new \PDO($dsn, $username, $password, $options);
                                
            } catch (\PDOException $e) {
                $PDOConnect = [ "error" => [
                    "message" => $e->getMessage(),
                    "code" => $e->getCode()
                ]];
                
            } finally {
                
                self::$PDOConnect = $PDOConnect;
            }
        }
        
        return self::$PDOConnect;
    }
    
    public static function disconnect()
    {
        self::$PDOConnect = null;
    }

    public static function getPDOConnect()
    {
        return self::$PDOConnect;
    }
    
    public static function getDrive()
    {
        return self::$driver;
    }
    
    public static function getHost()
    {
        return self::$host;
    }
    
    public static function getDbname()
    {
        return self::$dbname;
    }
    
    public static function getUsernameAdmin()
    {
        return self::$usernameAdmin;
    }
    
    public static function getEmailAdmin()
    {
        return self::$emailAdmin;
    }
    
    public static function getPasswordAdmin()
    {
        return self::$passwordAdmin;
    }
    
    public static function setUserPublic($userPublic) 
    {
        self::$userPublic = $userPublic;
    }
    
    public static function setPasswordPublic($passwordPublic) 
    {
        self::$passwordPublic = $passwordPublic;
    }
    
    public static function setUsernameAdmin($usernameAdmin) 
    {
        self::$usernameAdmin = $usernameAdmin;
    }
    
    public static function setEmailAdmin($emailAdmin) 
    {
        self::$emailAdmin = $emailAdmin;
    }
    
    public static function setPasswordAdmin($passwordAdmin) 
    {
        self::$passwordAdmin = $passwordAdmin;
    }
    
    public static function reconnectToAdmin() 
    {
        self::disconnect();
        self::connect(self::$driver, self::$host, self::$dbname, self::$usernameAdmin, self::$passwordAdmin);
    }
    
    public static function reconnectToPublic() 
    {
        self::disconnect();
        self::connect(self::$driver, self::$host, self::$dbname, self::$userPublic, self::$passwordPublic);
    }


    public function run($sql, $args = NULL) 
    {
        if (!$args) {
             return $this->query($sql);
        }
        $stmt = $this->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}
