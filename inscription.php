<?php include 'User.php';       // On include pour créer une session et lire le fichier de classe ?>


<form action="" Method="POST" class="flex-column">
                    <label for="login">Nom d'utilisateur</label>
                    <input type="text" id="login" name="login" placeholder="Min. 5 caractères" require>

                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" require>

                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" require>

                    <label for="email">E-mail</label>
                    <input type="text" id="email" name="email" require>

                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" value="Bonjour@123" placeholder="Bonjour@123" require>

                    <label for="confPassword">Confirmation</label>
                    <input type="password" id="confPassword" name="confPassword" value="Bonjour@123" placeholder="Bonjour@123" require>

                    <input type="submit" id="mybutton" value="S'inscrire" ><br><br>

                    <?php 
                        if ($_POST != NULL){
                            $login = htmlspecialchars($_POST['login']);
                            $nom =  htmlspecialchars($_POST['nom']);
                            $prenom =  htmlspecialchars($_POST['prenom']);
                            $email =  htmlspecialchars($_POST['email']);
                            $password = htmlspecialchars($_POST['password']);
                            $confPassword = htmlspecialchars($_POST['confPassword']);

                            var_dump($_POST);

                            if ($password == $confPassword){
                                if ($user->checkPassword($password, $confPassword)){
                                    $user->register($login, $password, $confPassword, $email, $prenom, $nom);
                                }
                                else{
                                    echo " !! Le mot de passe doit contenir au moins 8 caractères dont
                                    1 lettre majuscule, 1 lettre minuscule, 1 caractère spéciale et 1 chiffre!!";
                                }
                            }
                            else{
                                echo "!! Les mots de passes ne sont pas identiques !!";
                            }
                        
                            //echo $user->checkPassword($password, $confPassword);
                        }
                        else{
                            echo "Tous les champs doivent être remplis !";
                        }
                    ?>  
                            <!-- Le message sera affiché en cas derreur -->
                </form>