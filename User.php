<?php 

    $connect = mysqli_connect('localhost', 'root', '', 'classes');

    
    class User {
        private $id;
        public $login;
        public $email;
        public $firstname;
        public $lastname;

        public function __construct(){
        }

        public function register($login, $password, $email, $firstname, $lastname){
            global $connect;
            $request = $connect->query("INSERT INTO `utilisateurs`(`login`, `password`, `email`, `firstname`, `lastname`)
                                             VALUES ('$login', '$password', '$email', '$firstname', '$lastname')");
            echo "L'utilisateur {$login} a bien été créé.";
        }
    }

    $padawan = new User();
    $padawan->register('Chtulhu', 'bonjour', 'anna@gmail.com', 'Anna', 'Gilg');

    //$padawan = new User('padawan', 'padawan@gmail.com', 'Fabien', 'Ricca');

    //var_dump($padawan);

    /*$req = "SELECT * FROM `utilisateurs`";
    $request = mysqli_query($connect, $req);
    $data = mysqli_fetch_assoc($request);
    var_dump($data);*/
?>