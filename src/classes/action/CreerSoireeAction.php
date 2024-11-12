<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\exception\AuthnException;
use nrv\nancy\repository\NrvRepository;

class CreerSoireeAction extends Action{
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
                $res = <<<HTML
                <div class="admin-box">
                <form class="admin-form" action="?action=creerSoiree" method="post">
                <h1 class="admin-text">Création d'une soirée</h1>
                    <input type="text" name="nomSoiree" placeholder="Nom soirée" required>
                    <input type="text" name="themeSoiree" placeholder="Thématique" required>
                    <input type="date" name="dateSoiree" required>
                    <input type="time" name="heureSoiree" required>
                    <input type="submit" value="Créer soirée" name="creerSoiree">
                </form>
                </div>
                HTML;

            }else{
                $nomSoiree = filter_var($_POST["nomSoiree"], FILTER_SANITIZE_SPECIAL_CHARS);
                $themeSoiree = filter_var($_POST["themeSoiree"], FILTER_SANITIZE_SPECIAL_CHARS);
                $dateSoiree = filter_var($_POST["dateSoiree"], FILTER_SANITIZE_SPECIAL_CHARS);
                $heureSoiree = filter_var($_POST["heureSoiree"], FILTER_SANITIZE_SPECIAL_CHARS);
                NrvRepository::getInstance()->addSoiree($nomSoiree, $themeSoiree, $dateSoiree, $heureSoiree);
                $res = "Soirée bien créer";
            }
        }catch(AuthnException $e){
            $res = "<p>Vous n'avez pas les autorisations requises.</p>";
        }
        return $header . $res;
    }
}