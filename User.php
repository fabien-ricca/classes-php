<?php 
    session_start();    // On ouvre la session


    
    class User {

        // ATTRIBUTS
        private $connect;
        private $id;
        public $login;
        public $email;
        public $firstname;
        public $lastname;

        // CONSTRUCTEUR
        public function __construct(){
            $this->connect = mysqli_connect('localhost', 'root', '', 'classes');        // On connecte la base de donnée
        }

        // METHODE POUR VERIFIER LA FORME DU MDP
        public function checkPassword($password){
            $password_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";
            $check = false;

                // On vérifie que le mdp remplisse les conditions   
                if(preg_match($password_regex, $password)){
                    $check = true;
                }
            return $check;
            
        }

        // METHODE POUR INSCRIPTION
        public function register($login, $password, $confPassword, $email, $firstname, $lastname){
            $this->connect;
            $req = "SELECT login FROM `utilisateurs` WHERE login = '$login'"; // On initie la requête pour chercher le login
            $request = mysqli_query($this->connect, $req);
            if (strlen($login) >= 5){
                if(mysqli_num_rows($request) < 1){      // Si le login n'existe pas, on crypte le mdp, on créé l'user et on redirige vers connexion.php
                    $cryptPassword = password_hash($password, PASSWORD_BCRYPT);
                    $request = $this->connect->query("INSERT INTO `utilisateurs`(`login`, `password`, `email`, `firstname`, `lastname`)
                                                     VALUES ('$login', '$cryptPassword', '$email', '$firstname', '$lastname')");
                    header("location: connexion.php");
                }
                else{       // Sinon message d'erreur
                    return "L'utilisateur {$login} existe déjà";
                }
            }
            else{           // Sinon message d'erreur
                return "Le login doit faire au minimum 5 caractères";
            }
            
        }

        // METHODE POUR CONNEXION
        public function connect($login, $password){
            $req = "SELECT * FROM `utilisateurs` WHERE login = '$login'"; // On initie la requête pour chercher le login
            $request = mysqli_query($this->connect, $req);

            if(mysqli_num_rows($request) == 1){                         // Si le login existe
                $data = mysqli_fetch_assoc($request);                   // on récupère les données de la bdd en assoc
                if (password_verify($password, $data['password'])){     // Si les mdp sont ok on créée les Sessions et on redirige
                    $_SESSION['id'] = $data['id'];
                    $_SESSION['login'] = $data['login'];
                    $_SESSION['password'] = $data['password'];
                    $_SESSION['email'] = $data['email'];
                    $_SESSION['nom'] = $data['lastname'];
                    $_SESSION['prenom'] = $data['firstname'];
                    header("location: accueil.php");
                }
                else{       // Sinon message d'erreur
                    return "!! Identifiant ou mot de passe incorrect !!";
                }
            }
            else{       // Sinon message d'erreur
                return "!! Identifiant ou mot de passe incorrect !!";
            }
        }

        // METHODE POUR DECONNEXION
        public function disConnect(){
            session_destroy();                  // On détruit la session en cours
            header("location: connexion.php");  // On redirige vers la page de connexion
        }

        // METHODE POUR VERIFIER SI UN USER EST CONNECTE
        public function isConnected(){
            if($_SESSION['login']){     // Si une Session de login existe on return true
                return true;
            }
        }

        // METHODE POUR SUPPRIMER UN COMPTE USER
        public function delete($id){
            $this->connect;     
            $req = "DELETE FROM `utilisateurs` WHERE id = '$id'";       // On initie la requete pour supprimer
            $request = mysqli_query($this->connect, $req);
            $this->disConnect();        // On appelle la méthode disconnect pour détruire la session et rediriger vers la page de connsexion
        }

    }

    // On lance une nouvelle instance de classe
    $user = new User();
?>