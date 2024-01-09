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

$socket = new Socket("send");
$socket->open();
$socket->onSend("+START_SEND");
$cts = 0;

$memory = new Memcached();
$memory->addServer('127.0.0.1', 11211);
while (true) {
    if (($data = $memory->get("sendData"))) {
        while ($cts === 0) {
            $cts = intval($socket->onReceive());
            usleep(250000);
        }


        $socket->onSend("+" . $data);
        $cts = 0;

        $memory->set("sendData", false, 0);
    }

    usleep(250000);
}