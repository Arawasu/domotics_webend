<?php

/**
 * Class Client
 */
class Client extends BaseController {
    /**
     * Client constructor.
     * @param $database
     */
    public function __construct($database) {
        parent::__construct($database);

        $this->db->saveInMem("socketErr", false);
        $this->db->saveInMem("socketLoading", false);

    }

    /**
     * Load the ui for the client
     */
    public function ui() {
        $this->startSocket();

        $ser = new Serialization();
        $data = ['serverData' => $ser->deserialize("+BE;LE:0,DR:0,SW:0|+ST;TR:0,DR:0|+SC;LE:0,BS:0|+ZU;SW:0,RO:0,ZO:0|+MU;LD:0,LS:0,VE:0,DI:0|+KO;TE:0,DS:0,KE:0|+DE;LE:0,SE:0,SW:0")];

        $this->loadView(__FUNCTION__, $data);
    }

}