<?php
$time_start = microtime(true);

$deserialized = [];
$firstLayer = [];
$secondLayer = [];
$serialized = "+BE;LE:1,DR:0,SW:1|+ST;TR:1,DS:0|+SC;LE:1,BS:0|+ZU;SW:1,RO:1,ZO:0|+MU;MU:1,LD:0,LE:1,DI:1|+KO;TE:1,DE:0,KO:1|+DE;LE:1,SE:0,SW:1";

$firstLayer = explode("|", $serialized);

foreach ($firstLayer as $firstLayerVal) {
    $secondLayer = explode(";", $firstLayerVal);
    foreach ($secondLayer as $secondLayerVal) {
        if ($secondLayerVal[0] == "+") {
            $class = substr($secondLayerVal, 1);
        } else {
            $thirdLayer = explode(",", $secondLayerVal);
            //            $deserialized[$class] = $thirdLayer;
            foreach ($thirdLayer as $thirdLayerVal) {
                $fourthLayer = explode(":", $thirdLayerVal);
                $deserialized[$class][$fourthLayer[0]] = $fourthLayer[1];
            }
        }
    }
}

dump_exit($deserialized);
echo 'Total execution time in seconds: ' . sprintf("%f", (microtime(true) - $time_start));
