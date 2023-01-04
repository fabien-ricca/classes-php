<?php include 'User.php';       // On include pour créer une session et lire le fichier de classe ?>

<form action="" Method="POST" class="flex-column">
    <label for="login">Nom d'utilisateur</label>
    <input type="text" id="login" name="login" placeholder="Min. 5 caractères" require>

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" value="Bonjour@123" placeholder="Bonjour@123" require>

    <input type="submit" id="mybutton" value="Se connecter" ><br><br>

    <?php 
        if ($_POST != NULL){
            $login = htmlspecialchars($_POST['login']);
            $password = htmlspecialchars($_POST['password']);

            //echo $user->connect($login, $password);
            var_dump($user->connect($login, $password));
        }

        var_dump($_SESSION);
    ?>  
</form>