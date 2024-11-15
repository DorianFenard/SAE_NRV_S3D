<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\exception\AuthnException;

/**
 * Action déclenchée lorsque l'on veut ajouter un nouvel utilisateur,
 * si $http_method != "GET" on affiche le formulaire, sinon on l'exécute
 * elle vérifie si l'utilisateur à l'origine de cette action est bien connecté et est bien admin
 */
class CreerCompte extends Action{

    public function execute(): string
    {
        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';
        $adminButton = isset($_SESSION['role']) && $_SESSION['role'] >= 50
            ? '<a class="admin-button" href="?action=adminpage">ADMIN</a>'
            : '';
        $header = '<header class="program-header"><a class="home" href="?action=default">
                        <img class="program-icon" src="./images/icone.png" alt="NRV">
                    </a> <div class="menu">
                        <a class="list-button" href="?action=list">MA LISTE</a>
                        <a class="program-button" href="?action=program">PROGRAMME</a>'.
            $adminButton.
            $loginButton .'              
                    </div>
                    </header>
                    <div class="filters">';
        try{
            AuthnProvider::getSignedInUser();
            if(isset($_SESSION['role']) && $_SESSION['role'] === 100){
                if($this->http_method === "GET"){
                    $res = <<<HTML
                    <div class="admin-box">
                        <form class="admin-form" method="POST" action="?action=creerCompte">
                            <h1 class="admin-text">CREATION</h1>
                            <label for="email"></label>
                            <input type="texte" id="email" name="email" placeholder="email" required>
                            <br>
                            <label for="password"></label>
                            <input type="password"  id="password" name="password" placeholder="password" required>
                            <br>
                            <label for="passwordverif"></label>
                            <input type="password" id="password" name="passwordverif" placeholder="Vérifier password" required>
                            <br>
                            <input type="submit"  id="submit-button" value="Créer compte">
                             <a class="create-acc-home-button" href="?action=default">retourner à l'accueil</a>
                        </form>             
                    </div>
                    HTML;
                }else{
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    $password2 = $_POST['passwordverif'];
                    if($password != $password2){
                        $res = "<p> Le mot de passe choisi ne correspondait pas</p>";
                    }else{
                        try{
                            AuthnProvider::register($email, $password);
                            $res = "<p> Création du compte réussie </p>";
                        }catch (AuthnException $e) {
                            $res = "<p>" . $e->getMessage() . "</p>";
                        }
                    }
                }
            }
        }catch (AuthnException $e){
            $res = "<p> Vous n'avez pas l'autorisation requise</p>";
        }
        return $header . $res;
    }
}