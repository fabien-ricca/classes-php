<?php 
    class User {
        private $id;
        private $login;
        private $email;
        private $firstname;
        private $lastname;

        public function __construct($login, $email, $firstname, $lastname){
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
        }
    }

?>