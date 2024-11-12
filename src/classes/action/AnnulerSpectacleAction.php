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
                $res = "Liste des spectacles : ";
                foreach($listeSpec as $spec){
                    $res = $res . $spec->titre . ", annulé : " . $spec->estAnnule;
                }
            }else{

            }
        }catch (AuthnException $e){
            $res = "<p> Vous n'avez pas l'autorisation requise </p>";
        }
        return $res;
    }
}