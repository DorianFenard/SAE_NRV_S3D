<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\exception\AuthnException;
use nrv\nancy\repository\NrvRepository;

class AnnulerSpectacleAction extends Action {

    public function execute(): string
    {
        // TODO: Implement execute() method.
        try{
            AuthnProvider::getSignedInUser();
            if($this->http_method == "GET"){
                $listeSpec = NrvRepository::getInstance()->getAllSpectacle();
                $res = "<p>Liste des spectacles : <br></p>";
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
        return $res;
    }
}