<?php
class Admin extends BaseController {
    /**
     * Admin constructor.
     * @param $database
     */
    public function __construct($database) {
        parent::__construct($database);

        session_start();
        $this->db->saveInMem("socketErr", false);
        $this->db->saveInMem("socketLoading", false);
    }

    /**
     * Show login page & check if user is logged in
     */
    public function login() {
      if ($this->checkIfLoggedIn()){
        header("Location: http://domotica.local/admin/panel");
        exit;
      }

      $data = ["badlogin" => false];
        if ($this->db->getFromMem("wrongPass") === true){
            $data = ["badlogin" => true];
            $this->db->removeFromMem("wrongPass");
        }

        if ($_POST) {
            $user = $this->db->getUser($_POST["name"], hash("sha256", $_POST["password"]));

            if ($user == "NO_USER"){
              $this->db->saveInMem("wrongPass", true);
              $this->redirectToLogin();
            }

            $_SESSION["login"] = "true";
            header("Location: http://domotica.local/admin/panel");
            exit;

        }

        $this->loadView(__FUNCTION__, $data);
    }

    /**
     * Show panel page
     */
    public function panel() {
        if ($this->checkIfLoggedIn() === false){
          $this->redirectToLogin();
        }

        $this->startSocket();

        $ser = new Serialization();
        $data = ['serverData' => $ser->deserialize("+BE;LE:0,DR:0,SW:0|+ST;TR:0,DR:0|+SC;LE:0,BS:0|+ZU;SW:0,RO:0,ZO:0|+MU;LD:0,LS:0,VE:0,DI:0|+KO;TE:0,DS:0,KE:0|+DE;LE:0,SE:0,SW:0")];

        $this->loadView(__FUNCTION__, $data);
    }

    /**
     * Redirect to login page
     */
    private function redirectToLogin(){
      header("Location: http://domotica.local/admin/login");
      exit;
    }

    /**
     * Check if user is logged in by checking session data
     * @return bool
     */
    public function checkIfLoggedIn(){
      if (isset($_SESSION["login"])){
        if ($_SESSION["login"] === "true"){
          return true;
        }
      }

      return false;
    }
}
