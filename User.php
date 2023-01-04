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

            if($this->isConnected()){                               // Si un utilisateur est connecté
                $this->id = $_SESSION['id'];                        // On attribut les valeurs aux propriétés à partir des sessions
                $this->login = $_SESSION['login'];
                $this->email =$_SESSION['email'];
                $this->firstname =$_SESSION['nom'];
                $this->lastname =$_SESSION['prenom'];
            }
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
        public function register($login, $password, $email, $firstname, $lastname){
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
                    $_SESSION['id'] = $data['id'];                      // On créé des variables de session
                    $_SESSION['login'] = $data['login'];
                    $_SESSION['password'] = $data['password'];
                    $_SESSION['email'] = $data['email'];
                    $_SESSION['nom'] = $data['lastname'];
                    $_SESSION['prenom'] = $data['firstname'];
                    header("location: accueil.php");                    // On redirige vers la page d'accueil
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
            if(isset($_SESSION['login'])){     // Si une Session de login existe on return true
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

        // METHODE POUR UPDATE LES INFOS DE L'UTILISATEUR
        public function update($login, $password, $confPassword, $email, $firstname, $lastname){

        }

        // METHODE POUR RECUPERER TOUTES LES INFOS DE L'UTILISATEUR CONNECTE
        public function getAllInfos(){
            /*$req = "SELECT * FROM `utilisateurs` WHERE login = '$login'"; // On initie la requête pour chercher le login
            $request = mysqli_query($this->connect, $req);
            $data = mysqli_fetch_assoc($request);*/
            return <<<HTML
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Login</th>
                                    <th>E-mail</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                </tr>
                            </thead>
                        
                            <tbody>
                                <tr>
                                    <td>{$this->id}</td>
                                    <td>{$this->login}</td>
                                    <td>{$this->email}</td>
                                    <td>{$this->firstname}</td>
                                    <td>{$this->lastname}</td>
                                </tr>
                            </tbody>
                        </table>
                    HTML;
        }

        // METHODE POUR RECUPERER LE LOGIN
        public function getLogin(){
            return "Le login de l'utilisateur est  : {$this->login}";
        }

        // METHODE POUR RECUPERER L'EMAIL
        public function getEmail(){
            return "L'email de l'utilisateur est  : {$this->email}";
        }

        // METHODE POUR RECUPERER LE FIRSTNAME
        public function getFirstname(){
            return "Le prénom de l'utilisateur est : {$this->firstname}";
        }

        // METHODE POUR RECUPERER LE LASTNAME
        public function getLastname(){
            return "Le nom de l'utilisateur est : {$this->lastname}";
        }
    }

    $user = new User();         // On lance une nouvelle instance de classe
?>