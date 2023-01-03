<?php include 'User.php';       // On include pour créer une session et lire le fichier de classe
    //var_dump($_SESSION);

    if ($user->isConnected()){
        echo "Bonjour {$_SESSION['login']}";
    }
    else{
        header("location: connexion.php");
    }

    if(isset($_POST['deconnexion'])) {
        $user->disConnect();
    }

    if(isset($_POST['delete'])){
        $user->delete($_SESSION['id']);
    }
?>


<form class="form" action="" method="POST">
    <input type="submit" id="mybutton" name="deconnexion" value="Me déconnecter" >

    <input type="submit" id="mybutton" name="delete" value="Supprimer mon compte" >
</form>

