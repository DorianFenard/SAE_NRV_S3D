<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\auth\Authz;
use nrv\nancy\exception\AuthnException;
use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\repository\NrvRepository;

class AddSpec2Soiree extends Action{

    public function execute(): string
    {
        try{
            AuthnProvider::getSignedInUser();
            if($this->http_method == "GET"){
                $selecSpec = "<select name='selecSpec' id='selecSpec'>";
                $listeSpec = NrvRepository::getInstance()->getAllSpectacle();
                foreach($listeSpec as $spec){
                    $selecSpec .= "<option value='" . $spec->id . "'>" . $spec->titre . "</option>";
                }
                $selecSpec .= "</select>";

                $selecSoir = "<select name='selecSoiree' id='selecSoiree'>";
                $listeSoir = NrvRepository::getInstance()->getAllSoiree();
                foreach($listeSoir as $soiree){
                    $selecSoir .= "<option value='" . $soiree->id . "'>" . $soiree->nom . "</option>";
                }
                $selecSoir .= "</select>";

                $res = <<<HTML
                <form method="post" action="?action=addSpec2Soiree">
                $selecSpec
                $selecSoir
                <input type="submit" value="Ajouter spectacle" name="ajouterSpec">
                </form>
                HTML;
            }else{
                $soiree = $_POST['selecSoiree'];
                $spec = $_POST['selecSpec'];
                $succes = NrvRepository::getInstance()->addSpec2Soiree((int) $spec, (int) $soiree);
                if($succes){
                    $res = "Insertion réussie";
                }else{
                    $res = "Echec de l'insertion";
                }
            }
        }catch(AuthnException $e){
            $res = "<p> Vous n'avez pas l'autorisation requise </p>";
        }
        return $res;
    }
}