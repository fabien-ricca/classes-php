<?php include 'User.php';       // On include pour créer une session et lire le fichier de classe



    if(isset($_POST['deconnexion'])) {
        $user->disConnect();
    }

    if(isset($_POST['delete'])){
        $user->delete();
    }

    echo $user->getAllInfos() . '<br>';

    echo 'Login: ' . $user->getLogin() . '<br>';

    echo 'Email: ' . $user->getEmail() . '<br>';

    echo 'Firstname: ' . $user->getFirstname() . '<br>';

    echo 'Lastname: ' . $user->getLastname() . '<br>';

    var_dump($_SESSION);
?>




<form class="form" action="" method="POST">
    <input type="submit" id="mybutton" name="deconnexion" value="Me déconnecter" >
    <br><br>
    <input type="submit" id="mybutton" name="delete" value="Supprimer mon compte" >
</form>

<br><a href="profil.php">Modifier mon profil</a>