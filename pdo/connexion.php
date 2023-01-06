<?php include 'User.php';       // On include pour créer une session et lire le fichier de classe
    
    if ($_POST != NULL){
        $login = htmlspecialchars($_POST['login']);
        $password = htmlspecialchars($_POST['password']);

        $user->connect($login, $password);
        //header("location: accueil.php");
    }
?>

<form action="" Method="POST" class="flex-column">
    <label for="login">Nom d'utilisateur</label>
    <input type="text" id="login" name="login" placeholder="Min. 5 caractères" require>

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" value="Bonjour@123" placeholder="Bonjour@123" require>

    <input type="submit" id="mybutton" value="Se connecter" ><br><br>
    <p><?= $user->getMsg();?></p>
</form>