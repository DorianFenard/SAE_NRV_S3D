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
                $res = <<<HTML
        <form method="POST" action="?action=creerCompte">
            <label for="email">Entrez l'email :</label>
            <input type="texte" id="email" name="email" placeholder="email" required>
            <br>
            <label for="password">Entrez le mot de passe :</label>
            <input type="password"  id="password" name="password" placeholder="password" required>
            <br>
            <label for="passwordverif">Entrez le mot de passe :</label>
            <input type="password" id="password" name="passwordverif" placeholder="Vérifier password" required>
            <br>
            <input type="submit" value="Créer compte">
        </form>
HTML;

            }
        }catch (AuthnException $e){
            $res = "<p> Vous n'avez pas l'autorisation requise</p>";
        }
        return $res;
    }
}