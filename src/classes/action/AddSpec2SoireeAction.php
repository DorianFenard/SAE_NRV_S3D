<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\auth\Authz;
use nrv\nancy\exception\AuthnException;
use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\repository\NrvRepository;

class AddSpec2SoireeAction extends Action
{

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
        try {
            AuthnProvider::getSignedInUser();
            if ($this->http_method == "GET") {
                $selecSpec = "<select name='selecSpec' id='selecSpec'>";
                $listeSpec = NrvRepository::getInstance()->getAllSpectacle();
                foreach ($listeSpec as $spec) {
                    $selecSpec .= "<option value='" . $spec->id . "'>" . $spec->titre . "</option>";
                }
                $selecSpec .= "</select>";

                $selecSoir = "<select name='selecSoiree' id='selecSoiree'>";
                $listeSoir = NrvRepository::getInstance()->getAllSoiree();
                foreach ($listeSoir as $soiree) {
                    $selecSoir .= "<option value='" . $soiree->id . "'>" . $soiree->nom . "</option>";
                }
                $selecSoir .= "</select>";

                $res = <<<HTML
                <div class="admin-box">
                <form class="admin-form" method="post" action="?action=addSpec2Soiree">
                <h1 class="admin-text">Association Soirée/Spectacle</h1>
                $selecSpec
                $selecSoir
                <input type="submit" value="Ajouter spectacle" name="ajouterSpec">
                </form>
                </div>
                HTML;
            } else {
                $soiree = $_POST['selecSoiree'];
                $spec = $_POST['selecSpec'];
                $succes = NrvRepository::getInstance()->addSpec2Soiree((int)$spec, (int)$soiree);
                if ($succes) {
                    $res = "Insertion réussie";
                } else {
                    $res = "Echec de l'insertion";
                }
            }
        } catch (AuthnException $e) {
            $res = "<p> Vous n'avez pas l'autorisation requise </p>";
        }
        return $header . $res;
    }
}