<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\exception\AuthnException;
use nrv\nancy\repository\NrvRepository;

class AnnulerSpectacleAction extends Action {

    public function execute(): string
    {
        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';
        $adminButton = isset($_SESSION['role']) && $_SESSION['role'] === 100
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
            if($this->http_method == "GET"){
                $listeSpec = NrvRepository::getInstance()->getAllSpectacle();
                $res = " <div class='admin-box'><div class='admin-form'><h1 class='admin-text'>Liste des spectacles : <br></h1>";
                foreach($listeSpec as $spec){
                    $annulation = ($spec->estAnnule ?? true) ? "Oui" : "Non";
                    $res .= <<<HTML
                     <form method="POST" action="?action=annulerSpec">
                        <p>$spec->titre annulé : $annulation</p>
                        <input type="hidden" name="specChoisi" value="$spec->id">
                        <input type="hidden" name="annulation" value="$annulation">
                        <input type="submit" value="Changer">
                    </form>
                
                HTML;
                }
                $res .= "</div></div>";
            }else{
                $idSpec = $_POST['specChoisi'];
                $annulation = $_POST['annulation'];
                if($annulation === "Oui"){
                    $succes = NrvRepository::getInstance()->changerAnnulation((int) $idSpec, false);
                }else{
                    $succes = NrvRepository::getInstance()->changerAnnulation((int) $idSpec, true);
                }
                if($succes){
                    $res = "Changement réussi";
                }else{
                    $res = "Echec du changement";
                }

            }
        }catch (AuthnException $e){
            $res = "<p> Vous n'avez pas l'autorisation requise </p>";
        }
        return $header . $res;
    }
}