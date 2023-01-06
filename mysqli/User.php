<?php 
    session_start();    // On ouvre la session


    
    class User {

        // ATTRIBUTS
        private $connect;
        private $id;
        public $login;
        public $password;
        public $email;
        public $firstname;
        public $lastname;

        // CONSTRUCTEUR
        public function __construct(){
            $this->connect = mysqli_connect('localhost', 'root', '', 'classes');        // On connecte la base de donnée
        }

        // SETTER
        public function setUser($id, $login, $password, $email, $firstname, $lastname){
            $this->id = $id;
            $this->login = $login;
            $this->password = $password;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
        }

        // METHODE POUR VERIFIER LA FORME DU MDP
        public function checkPassword($password){
            $password_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";       // On créé la regex
            $check = false;     // On initie le check sur false

                // On vérifie que le mdp remplisse les conditions, si oui on passe check sur true   
                if(preg_match($password_regex, $password)){
                    $check = true;
                }
            return $check;      // On return check (true ou false)
            
        }

        // METHODE POUR INSCRIPTION
        public function register($login, $password,  $email, $firstname, $lastname){
            $this->connect;     // On relie la connexion à la base de donnée
            $req = "SELECT login FROM `utilisateurs` WHERE login = '$login'"; // On initie la requête pour chercher le login
            $request = mysqli_query($this->connect, $req);
            if (strlen($login) >= 5){       // Si le login fait 5 caractères ou plus
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
            $this->connect;     // On relie la connexion à la base de donnée
            $req = "SELECT * FROM `utilisateurs` WHERE login = '$login'"; // On initie la requête pour chercher le login
            $request = mysqli_query($this->connect, $req);

            if(mysqli_num_rows($request) == 1){                         // Si le login existe (s'il correspond)
                $data = mysqli_fetch_assoc($request);                   // on récupère les données de la bdd en assoc
                if (password_verify($password, $data['password'])){     // Si le mdp correspond

                    // On créé un nouvel objet avec les données de l'utilisateur qui vient de se connecter, et on le stocke dans une session
                    $user1 = new User();
                    $user1->setUser($data['id'], $data['login'], $data['password'], $data['email'], $data['firstname'], $data['lastname']);
                    $_SESSION['user1'] = $user1;
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
            if(isset($_SESSION['user1']->login)){     // Si un attribut 'login' est stocké dans un objet 'user1' existe on return true (si un user est connecté)
                return true;
            }
        }

        // METHODE POUR SUPPRIMER UN COMPTE USER
        public function delete($id){
            $this->connect;     // On relie la connexion à la base de donnée
            $req = "DELETE FROM `utilisateurs` WHERE id = '$id'";       // On initie la requete pour supprimer
            $request = mysqli_query($this->connect, $req);
            $this->disConnect();        // On appelle la méthode disconnect pour détruire la session et rediriger vers la page de connsexion
        }

        // METHODE POUR UPDATE LES INFOS DE L'UTILISATEUR
        public function update($login, $email, $oldPassword, $newPassword, $confNewPassword){
            $this->connect;
            $id = $_SESSION['user1']->id;

            if (password_verify($oldPassword, $_SESSION['user1']->password)){
                if ($newPassword == $confNewPassword){
                    if($this->checkPassword($newPassword)){
                        $cryptPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                        $req = $this->connect->query("UPDATE `utilisateurs` SET password='$cryptPassword' WHERE id='$id'");
                        $_SESSION['user1']->password = $cryptPassword;
                        header("location: profil.php");
                    }
                    else{
                        echo "!! Les nouveaux mot de passes ne sont pas identiques !!";
                    }
                }
            }
            else{       // Sinon message d'erreur
                echo "!! Le mot de passe actuel est incorrect !!";
            }

            // Si le login est différent
            if ($login != $this->login){
                $req = "SELECT login FROM `utilisateurs` WHERE login = '$login'";
                $request = mysqli_query($this->connect, $req);

                // S'il n'existe pas
                if(mysqli_num_rows($request) < 1){ 
                    $req = $this->connect->query("UPDATE `utilisateurs` SET login='$login' WHERE id='$id'");
                    $_SESSION['user1']->login = $login;
                    header("location: profil.php");
                }
                else{       // Sinon message d'erreur
                    echo "L'utilisateur {$login} existe déjà.";
                }
            }

            // Si l'email est différent
            if ($email != $this->email){
                $req = "SELECT email FROM `utilisateurs` WHERE email = '$email'";
                $request = mysqli_query($this->connect, $req);

                // S'il n'existe pas
                if(mysqli_num_rows($request) < 1){
                    $req = $this->connect->query("UPDATE `utilisateurs` SET email='$email' WHERE id='$id'");
                    $_SESSION['user1']->email = $email;
                    header("location: profil.php");
                
                }
                else{       // Sinon message d'erreur
                    echo "L'email {$email} est déjà utilisée.";
                }
            }
        }

        // METHODE POUR RECUPERER TOUTES LES INFOS DE L'UTILISATEUR CONNECTE
        public function getAllInfos(){
            /*$req = "SELECT * FROM `utilisateurs` WHERE login = '$login'"; // On initie la requête pour chercher le login
            $request = mysqli_query($this->connect, $req);
            $data = mysqli_fetch_assoc($request);
            return $data;*/
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
                                    <td>{$_SESSION['user1']->id}</td>
                                    <td>{$_SESSION['user1']->login}</td>
                                    <td>{$_SESSION['user1']->email}</td>
                                    <td>{$_SESSION['user1']->firstname}</td>
                                    <td>{$_SESSION['user1']->lastname}</td>
                                </tr>
                            </tbody>
                        </table>
                    HTML;
        }

        // METHODE POUR RECUPERER LE LOGIN
        public function getLogin(){
            return $_SESSION['user1']->login;
        }

        // METHODE POUR RECUPERER L'EMAIL
        public function getEmail(){
            return $_SESSION['user1']->email;
        }

        // METHODE POUR RECUPERER LE FIRSTNAME
        public function getFirstname(){
            return $_SESSION['user1']->firstname;
        }

        // METHODE POUR RECUPERER LE LASTNAME
        public function getLastname(){
            return $_SESSION['user1']->lastname;
        }
    }

    $user = new User();         // On lance une nouvelle instance de classe
?>