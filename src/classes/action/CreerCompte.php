<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\exception\AuthnException;

class CreerCompte extends Action{

    public function execute(): string
    {
        try{
            AuthnProvider::getSignedInUser();
            if(isset($_SESSION['role']) && $_SESSION['role'] === 100){
                if($this->http_method === "GET"){
                    $res = <<<HTML
                    <div class="create-acc-box">
                        <form class="create-acc-form" method="POST" action="?action=creerCompte">
                            <h1 class="create-acc-text">CONNEXION</h1>
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
        return $res;
    }
}