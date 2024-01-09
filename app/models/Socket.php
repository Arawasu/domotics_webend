<?php

/**
 * Class Socket
 */
class Socket {
    private $server = '127.0.0.1';
    private $port = '1118';
    private $socket = false;
    public $lastError;
    private $memory;

    /**
     * Socket constructor.
     * @param string $method
     */
    public function __construct($method = "read") {
        set_time_limit(0);

        $this->memory = new Memcached();
        $this->memory->addServer('127.0.0.1', 11211);

        if ($method == "send") {
            $this->port = "1119";
        } elseif ($method == "read") {
            $this->port = "1118";
        }
    }

    /**
     * Execute the file that writes to memory with the data it fetches from the RPI via sockets.
     * This file is executed via shell. /usr/bin/nohup makes sure the php program keeps running even when the shell is closed.
     * >/dev/null 2>&1 & makes sure the output of the shell is discarted.
     */
    public function execSocket() {
        $command = "php " . ROOT . "/app/exec/exec_socket_read.php";
        shell_exec("/usr/bin/nohup " . $command . " >/dev/null 2>&1 &");
    }

    /**
     * Execute the file that reads from memory with the data that is set in the frontend.
     * This file is executed via shell. /usr/bin/nohup makes sure the php program keeps running even when the shell is closed.
     * >/dev/null 2>&1 & makes sure the output of the shell is discarted.
     */
    public function execSocketSend() {
        $command = "php " . ROOT . "/app/exec/exec_socket_send.php";
        shell_exec("/usr/bin/nohup " . $command . " >/dev/null 2>&1 &");
    }

    /**
     * Wrapper function for when data is received.
     * @return bool|string
     */
    public function onReceive() {
        $receivedData = $this->receiveData();
        return $receivedData;
    }

    /**
     * Wrapper function for when data needs to be send
     * @param $data
     */
    public function onSend($data) {
        $this->sendData($data);
    }


    /**
     * Wrapper function for opening a socket.
     * This function has built-in retrying and error handling.
     */
    public function open() {
        $retries = 0;
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);

        $conn = false;

        while ($conn === false) {
            $retries++;
            $conn = @socket_connect($this->socket, $this->server, $this->port);

            if ($conn === false) {
                $this->memory->set('socketLoading', true, 0);
                usleep(1000000);
            }

            if ($retries >= 25) {
                $this->memory->set('socketErr', true, 0);
                $this->memory->set('execSend', false, 0);
                $this->close($this->socket);
                exit;
            }
        }

        $this->memory->set('socketLoading', false, 0);
    }

    /**
     * Wrapper function for receiving data.
     * This function has built-in retrying and error handling.
     * @return bool|string
     */
    public function receiveData() {
        $retries = 0;
        $data = @socket_read($this->socket, 1024);
        var_dump($data);

        while (empty($data)) {
            $retries++;
            $data = @socket_connect($this->socket, $this->server, $this->port);

            if (empty($data)) {
                $this->memory->set('socketLoading', true, 0);
                usleep(1000000);
            }

            if ($retries >= 25) {
                $this->memory->set('socketErr', true, 0);
                $this->memory->set('execSend', false, 0);
                $this->close($this->socket);
                exit;
            }
        }

        return $data;
    }

    /**
     * Wrapper function for sending data.
     * This function has built-in retrying and error handling.
     * @return bool|string
     */
    public function sendData($data) {
        $retries = 0;
        $data = @socket_write($this->socket, $data, strlen($data));

        while (empty($data)) {
            $retries++;
            $data = @socket_connect($this->socket, $this->server, $this->port);

            if (empty($data)) {
                $this->memory->set('socketLoading', true, 0);
                usleep(1000000);
            }

            if ($retries >= 25) {
                $this->memory->set('socketErr', true, 0);
                $this->memory->set('execSend', false, 0);
                $this->close($this->socket);
                exit;
            }
        }
    }

    /**
     * Wrapper function for closing sockets
     * @param $socket
     */
    public function close($socket) {
        socket_close($socket);
    }

}