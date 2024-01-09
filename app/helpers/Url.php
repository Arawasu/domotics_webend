<?php

class Url {
    // Return json data.
    public function returnJsonData($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}