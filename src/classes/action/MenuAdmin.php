<?php
declare(strict_types=1);
namespace nrv\nancy\action;

class MenuAdmin extends Action{

    public function execute(): string
    {
        $res = <<<HTML
        <ul>
            <li><a href="index.php?action=creerSpectacle" >Créer un spectacle</li>
            <li><a href="index.php?action=creerSoiree" >Créer une soirée</li>
            <li><a href="index.php?action=addSpec2Soiree" >Associer un spectacle à une soirée</li>
            <li><a href="index.php?action=annulerSpec" >Annuler un spectacle</li>
            <li><a href="index.php?action=creerCompte" >Créer un compte staff</li>
        </ul>
        HTML;
        return $res;
    }
}