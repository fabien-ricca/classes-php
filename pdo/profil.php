<?php include 'User.php';       // On include pour créer une session et lire le fichier de classe 

    var_dump($_SESSION);
    $msgError = "";         //Création de la variable qui contiendra le message d'erreur du mdp

    if ($_POST != NULL){
        $login=htmlspecialchars($_POST['login']);
        $email=htmlspecialchars($_POST['email']);
        $newPassword=htmlspecialchars($_POST['password']);  
        $confNewPassword=htmlspecialchars($_POST['confpassword']); 
        $oldPassword=htmlspecialchars($_POST['oldpassword']);  

        var_dump($_POST);

        $user->update($login, $email, $oldPassword, $newPassword, $confNewPassword);
    }

?>


<form action="" Method="POST" class="flex-column">
    <label for="login">Nom d'utilisateur</label>
    <input type="text" id="login" name="login" placeholder="Min. 5 caractères"  value="<?= $user->getLogin() ?>" placeholder="<?= $user->getLogin() ?>">

    <label for="email">E-mail</label>
    <input type="text" id="email" name="email"  value="<?= $user->getEmail() ?>" placeholder="<?= $user->getEmail() ?>">

    <label for="oldpassword">Ancien mdp</label>
    <input type="password" id="oldpassword" name="oldpassword" value="Aurevoir!444" placeholder="Aurevoir!444">

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password">

    <label for="confpassword">Confirmation</label>
    <input type="password" id="confpassword" name="confpassword"">

    <input type="submit" id="mybutton" value="Modifier" ><br><br>

</form>