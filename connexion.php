<?php include 'User.php';       // On include pour créer une session et lire le fichier de classe
    $user = new User(); 
    
    if ($_POST != NULL){
        $login = htmlspecialchars($_POST['login']);
        $password = htmlspecialchars($_POST['password']);

        //echo $user->connect($login, $password);
        $user->connect($login, $password);
    }
?>

<form action="" Method="POST" class="flex-column">
    <label for="login">Nom d'utilisateur</label>
    <input type="text" id="login" name="login" placeholder="Min. 5 caractères" require>

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" value="Aurevoir!444" placeholder="Aurevoir!444" require>

    <input type="submit" id="mybutton" value="Se connecter" ><br><br>
</form>