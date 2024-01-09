<?php

/**
 * Class Exec
 */
class Exec extends BaseController {
    private $serialize;
    private $url;

    /**
     * Exec constructor.
     * @param $database
     */
    public function __construct($database) {
        parent::__construct($database);
        $this->serialize = new Serialization();
        $this->url = new Url();
    }

    /**
     * Write to the scoket if data is posted to this function.
     * Data is also intercepted if it's an AL_ME alert confirmation.
     * This data has a separate handler and won't be send to the server
     * Data that is received here will be saved in the memory and will he handled by the exec_socket_send file
     */
    public function socket_write() {
        if ($_POST['senddata'] === 'AL_ME') {
            $currentTime = ((int)date("H"));
            $medConfirmed = 0;

            switch (true) {
                case ($currentTime >= 18 || $currentTime < 8):
                    $medConfirmed = 18;
                    break;
                case ($currentTime >= 12 && $currentTime < 18):
                    $medConfirmed = 12;
                    break;
                case ($currentTime >= 8 && $currentTime < 12):
                    $medConfirmed = 8;
                    break;
            }

            $this->db->saveInMem("AL_ME", $medConfirmed);
            exit;
        }


        $this->db->saveInMem("sendData", $_POST['senddata']);
    }

    /**
     * Read data from the server gotten from the exec_socket_read file.
     * Also has built-in error handling and such.
     */
    public function socket_read() {

        if ($this->db->getFromMem("socketErr")) {
            $this->url->returnJsonData('error');
        }

        if ($this->db->getFromMem("socketLoading")) {
            $this->url->returnJsonData("loading_socket");
        }

        $readData = $this->db->getFromMem("received_data");
        $oldData = $this->db->getFromMem("old_received_data");

        if ($readData[0] != "+") {
            $this->url->returnJsonData('no_data');
        }



        if ($this->db->getFromMem("logLock") == false || empty($this->db->getFromMem("logLock"))) {
            $this->db->saveInMem("logLock", true);
            if ($readData != $oldData) {
                if (!empty($oldData)) {
                    $this->db->insertInLog($this->serialize->compare($this->serialize->deserialize($readData), $this->serialize->deserialize($oldData)));
                }

                $this->db->saveInMem("old_received_data", $this->db->getFromMem("received_data"));
            }

            $this->db->saveInMem("logLock", false);
        }

        $readData = $this->checkMedicineTime($this->serialize->deserialize($readData));
        $this->url->returnJsonData($readData);

    }

    /**
     * Get log items and construct them in a HTML format so they can be returned as JSON.
     */
    public function log_read() {
        $furni = new Furniture();
        $date = new DateTime();

        $logEntries = $this->db->getLog("log");
        $html = "";

        foreach ($logEntries as $logEntry) {
            $logData = unserialize($logEntry['data']);
            $date->setTimestamp($logEntry['timestamp']);
            $html .= "<div class='logDate'>" . $date->format('Y-m-d H:i:s') . "</div>";

            $html .= "<div class='logEntry'>";
            foreach ($logData as $furniCode => $senac) {
                $html .= $furni->getPrettyFurniNames($furniCode) . ": ";

                $lastSenac = end($senac);
                foreach ($senac as $senacCode => $senacData) {
                    if (empty($furni->getPrettyFurniNames($senacCode))) {
                        continue;
                    }

                    $html .= $furni->getPrettyFurniNames($senacCode) . " => ";
                    $html .= ($senacData == 1) ? "Aan " : "Uit ";
                    $html .= "<br />";

                    if (!$senacData == $lastSenac) {
                        $html .= ", ";
                    }
                }
            }

            $html .= "</div>";
            $html .= "<br />";
        }

        $this->url->returnJsonData($html);
    }

    /**
     * Check if it's time for the client's medicine.
     * @param $readData
     * @return mixed
     */
    private function checkMedicineTime($readData) {
        $tookMeds = $this->db->getFromMem('AL_ME');
        $currentTime = ((int)date("H"));
        $readData["AL"]["ME"] = 0;

        switch (true) {
            case ($currentTime >= 18 || $currentTime < 8) && $tookMeds != 18:
                $readData["AL"]["ME"] = 18;
                break;
            case ($currentTime >= 12 && $currentTime < 18) && $tookMeds != 12:
                $readData["AL"]["ME"] = 12;
                break;
            case ($currentTime >= 8 && $currentTime < 12) && $tookMeds != 8:
                $readData["AL"]["ME"] = 8;
                break;
        }

        return $readData;
    }
}
