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
        public $msg;

        // CONSTRUCTEUR
        public function __construct(){
            $this->connect = new PDO('mysql:host=localhost;dbname=classes', 'root', '');        // On connecte la base de donnée

            if($this->isConnected()){                               // Si un utilisateur est connecté
                $this->id = $_SESSION['id'];                        // On attribut les valeurs aux propriétés à partir des sessions
                $this->login = $_SESSION['login'];
                $this->password = $_SESSION['password'];
                $this->email =$_SESSION['email'];
                $this->firstname =$_SESSION['firstname'];
                $this->lastname =$_SESSION['lastname'];
            }
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
            $request = $this->connect->prepare("SELECT login FROM utilisateurs WHERE login = ?"); 
                $request->execute([$login]);
                $count = $request->rowCount();

            if (strlen($login) >= 5){       // Si le login fait 5 caractères ou plus
                
                if ($count < 1){      // Si le login n'existe pas, on crypte le mdp, on créé l'user et on redirige vers connexion.php
                    $cryptPassword = password_hash($password, PASSWORD_BCRYPT);
                    $req = $this->connect->prepare ("INSERT INTO `utilisateurs` (`login`, `password`, `email`, `firstname`, `lastname`) VALUES (?, ?, ?, ?, ?)");
                $req->execute(array($login, $cryptPassword, $email, $firstname, $lastname));
                    header("location: connexion.php");
                }

                else{       // Sinon message d'erreur
                    $this->msg = "L'utilisateur {$login} existe déjà";
                }
            }

            else{           // Sinon message d'erreur
                $this->msg = "Le login doit faire au minimum 5 caractères";
            }
            
        }

        // METHODE POUR CONNEXION
        public function connect($login, $password){
                $request = $this->connect->prepare("SELECT * FROM utilisateurs WHERE login = ?"); 
                $request->execute([$login]);
                $count = $request->rowCount();

            if($count == 1){                         // Si le login existe (s'il correspond)
                $data = $request->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $data['password'])){     // Si le mdp correspond
                    $_SESSION['id'] = $data['id'];
                    $_SESSION['login'] = $data['login'];
                    $_SESSION['password'] = $data['password'];
                    $_SESSION['email'] = $data['email'];
                    $_SESSION['firstname'] = $data['firstname'];
                    $_SESSION['lastname'] = $data['lastname'];

                    header("location: accueil.php");                    // On redirige vers la page d'accueil
                }
                else{       // Sinon message d'erreur
                    $this->msg = "!! Identifiant ou mot de passe incorrect !!";
                }
            }
            else{       // Sinon message d'erreur
                $this->msg = "!! Identifiant ou mot de passe incorrect !!";
            }
        }

        // METHODE POUR DECONNEXION
        public function disConnect(){
            session_destroy();                  // On détruit la session en cours
            header("location: connexion.php");  // On redirige vers la page de connexion
        }

        // METHODE POUR VERIFIER SI UN USER EST CONNECTE
        public function isConnected(){
            if(isset($_SESSION['login'])){     // Si un attribut 'login' est stocké dans un objet 'user1' existe on return true (si un user est connecté)
                return true;
            }
        }

        // METHODE POUR SUPPRIMER UN COMPTE USER
        public function delete(){
            $this->connect;     // On relie la connexion à la base de donnée
            $id = $_SESSION['id'];
            $request = $this->connect->prepare("DELETE FROM `utilisateurs` WHERE id = ?"); 
            $request->execute([$id]);
            $this->disConnect();        // On appelle la méthode disconnect pour détruire la session et rediriger vers la page de connsexion
        }

        // METHODE POUR UPDATE LES INFOS DE L'UTILISATEUR
        public function update($login, $email, $oldPassword, $newPassword, $confNewPassword){
            $this->connect;
            $id = $_SESSION['id'];

            if (password_verify($oldPassword, $_SESSION['password'])){
                if ($newPassword == $confNewPassword){
                    if($this->checkPassword($newPassword)){
                        $cryptPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                        $request = $this->connect->prepare("UPDATE `utilisateurs` SET password = ? WHERE id = ?"); 
                        $update = $request->execute(array($cryptPassword, $id));
                        $_SESSION['password'] = $cryptPassword;
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
            if ($login != $_SESSION['login']){
                $request = $this->connect->prepare("SELECT * FROM utilisateurs WHERE login = ?"); 
                $request->execute([$login]);
                $count = $request->rowCount();

                // S'il n'existe pas
                if($count < 1){ 
                    $request = $this->connect->prepare("UPDATE `utilisateurs` SET login = ? WHERE id = ?"); 
                    $update = $request->execute(array($login, $id));
                    $_SESSION['login'] = $login;
                    header("location: profil.php");
                }
                else{       // Sinon message d'erreur
                    echo "L'utilisateur {$login} existe déjà.";
                }
            }

            // Si l'email est différent
            if ($email != $_SESSION['email']){
                $request = $this->connect->prepare("SELECT * FROM utilisateurs WHERE email = ?"); 
                $request->execute([$email]);
                $count = $request->rowCount();

                // S'il n'existe pas
                if($count < 1){ 
                    $request = $this->connect->prepare("UPDATE `utilisateurs` SET email = ? WHERE id = ?"); 
                    $update = $request->execute(array($email, $id));
                    $_SESSION['email'] = $email;
                    header("location: profil.php");
                
                }
                else{       // Sinon message d'erreur
                    echo "L'email {$email} est déjà utilisée.";
                }
            }
        }

        public function getMsg(){
            return $this->msg;
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
                                    <td>{$_SESSION['id']}</td>
                                    <td>{$_SESSION['login']}</td>
                                    <td>{$_SESSION['email']}</td>
                                    <td>{$_SESSION['firstname']}</td>
                                    <td>{$_SESSION['lastname']}</td>
                                </tr>
                            </tbody>
                        </table>
                    HTML;
        }

        // METHODE POUR RECUPERER LE LOGIN
        public function getLogin(){
            return $_SESSION['login'];
        }

        // METHODE POUR RECUPERER L'EMAIL
        public function getEmail(){
            return $_SESSION['email'];
        }

        // METHODE POUR RECUPERER LE FIRSTNAME
        public function getFirstname(){
            return $_SESSION['firstname'];
        }

        // METHODE POUR RECUPERER LE LASTNAME
        public function getLastname(){
            return $_SESSION['lastname'];
        }
    }

    $user = new User();         // On lance une nouvelle instance de classe
?>