<?php

/**
 * Class Database
 */
class Database {
    private $mem;
    private $db;
    private $excludeLog = [
        "MU" => ["LD", "LS", "DI"],
        "SC" => ["SL"]

    ];

    /**
     * Database constructor.
     * @param bool $enableMemcached
     */
    function __construct($enableMemcached = false) {
        $this->connectMysqli();

        if ($enableMemcached == true) {
         $this->connectMemcached();
        }
    }

    /**
     * Connect to the mysql server
     */
    private function connectMysqli(){
        $this->db = new mysqli("localhost", "admin", "root", "domotics");

        if ($this->db->connect_errno){
            die("Could not connect to MYSQL: " . mysqli_connect_error());
        }
    }

    /**
     * Connect to the memcached server
     */
    private function connectMemcached(){
        $this->mem = new Memcached();
        $this->mem->addServer('localhost', 11211);
    }

    /**
     * Get a specific user from the database. Return the user if found, or return NO_USER if not.
     * @param $username
     * @param $password
     * @return mixed|string
     */
    public function getUser($username, $password){
        $query = $this->db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $query->bind_param("ss", $username, $password);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows == 0){
          return "NO_USER";
        }

        for ($set = array (); $row = $result->fetch_assoc(); $set[] = $row);

        return $set[0];
    }

    /**
     * Get the log from the database, limited to 50 records
     * @param $table
     * @return array
     */
    public function getLog($table){
        $result = $this->db->query("SELECT * FROM log ORDER BY timestamp DESC LIMIT 50");

        for ($set = array (); $row = $result->fetch_assoc(); $set[] = $row);

        return $set;
    }

    /**
     * Prepare log data and insert into database
     * @param $data
     * @return int
     */
    public function insertInLog($data){
        foreach ($this->excludeLog as $furni => $senAcs){
            foreach ($senAcs as $senAc){
              if (isset($data[$furni][$senAc])){
                unset($data[$furni][$senAc]);
                return 0;
              }
            }
        }

        $this->insert(serialize($data));
    }

    /**
     * Wrapper function for inserting things into the database.
     * @param $data
     */
    public function insert($data){
        $timestamp = time();

        $query = $this->db->prepare("INSERT INTO domotics.log (data, timestamp) VALUES (?, ?)");
        $query->bind_param("si", $data, $timestamp);

        $query->execute();

    }

    /**
     * Wrapper function for inserting things into memory
     * @param $key
     * @param $value
     */
    public function saveInMem($key, $value) {
        $this->mem->set($key, $value, 0);
    }

    /**
     * Wrapper function for getting things from the memory
     * @param $key
     * @return mixed
     */
    public function getFromMem($key) {
        return $this->mem->get($key);
    }

    /**
     * Wrapper function for removing things from the memory
     * @param $key
     */
    public function removeFromMem($key){
      $this->mem->delete($key, 0);
    }
}
