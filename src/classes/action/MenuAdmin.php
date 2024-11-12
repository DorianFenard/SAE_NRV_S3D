<?php
declare(strict_types=1);
namespace nrv\nancy\action;

class MenuAdmin extends Action{

    public function execute(): string
    {
        $res = <<<HTML
        <ul>
            <li><a href="" >Créer un spectacle</li>
            <li><a href="" >Créer une soirée</li>
            <li><a href="" >Associer un spectacle à une soirée</li>
            <li><a href="" >Annuler un spectacle</li>
            <li><a href="" >Créer un compte staff</li>
        </ul>
        HTML;
        return $res;
    }
}