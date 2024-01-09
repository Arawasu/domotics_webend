<?php
set_time_limit(0);

if (gethostname() == "Foxtrot") {
    $basepath = "/mnt/e/projects/domotica/website";
} elseif (gethostname() == "echo") {
    $basepath = "/mnt/c/projects/domotica/website";
} else {
    $basepath = "/var/www/domotica/website";
}

include $basepath . "/app/models/Socket.php";

$socket = new Socket("read");
$memory = new Memcached();
$memory->addServer('127.0.0.1', 11211);

$socket->open();
$socket->sendData("1");


while (true) {
    $data = $socket->onReceive();

    if ($data[0] == "+") {
        $memory->set("received_data", $data, 0);
        $socket->sendData("1");
        unset($data);
        usleep(50000);
        continue;
    }

    usleep(50000);
}