<?php

/**
 * Class BaseController
 */
class BaseController {
    protected $db;

    /**
     * BaseController constructor.
     * @param $database
     */
    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Load a view based on the view name which is passed to the function.
     * Data can also be passed to the function, which is "redefined". This trick
     * works because defining variables before including a file in the same scope will
     * give that included file access to all the variables defined before it
     * @param $view
     * @param bool $data
     */
    protected function loadView($view, $data = false) {
        if ($data) {
            foreach ($data as $key => $value) {
                $$key = $value;
            }
        }
        include ROOT . "/app/views/" . $view . ".php";
    }

    /**
     * Check if a socket is already running. If not, start a socket.
     */
    protected function startSocket() {
        if (empty($this->db->getFromMem('execSend'))) {
            if ($this->db->getFromMem('execSend') !== true) {
                $socket = new Socket();
                $socket->execSocket();
                $socket->execSocketSend();

                $this->db->saveInMem('execSend', true);
            }
        }
    }
}