<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\exception\AuthnException;
use nrv\nancy\repository\NrvRepository;
use nrv\nancy\auth\AuthnProvider;

class CreerSpectacleAction extends Action {
    public function execute(): string {
        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';
        $adminButton = isset($_SESSION['role']) && $_SESSION['role'] === 100
            ? '<a class="admin-button" href="?action=adminpage">ADMIN</a>'
            : '';
        $header = '<header class="program-header"><a class="home" href="?action=default">
                        <img class="program-icon" src="./images/icone.png" alt="NRV">
                    </a> <div class="menu">
                        <a class="list-button" href="DefaultAction.php">ACCUEIL</a>
                        <a class="list-button" href="?action=list">MA LISTE</a>
                        <a class="program-button" href="?action=program">PROGRAMME</a>'.
            $adminButton.
            $loginButton .'              
                    </div>
                    </header>
                    <div class="filters">';
        try{
            AuthnProvider::getSignedInUser();
            if ($this->http_method === "GET") {
                $res = <<<HTML
            <div class="admin-box">
            <form class="admin-form" action="?action=creerSpectacle" method="post">
            <h1 class="admin-text"> Création d'un spectacle</h1>;
                <label class="admin-element" for="nom">Nom</label>
                <input class="admin-element" type="text" name="nom" id="nom" required>

                <label class="admin-element" for="description">Description</label>
                <input class="admin-element" type="text" name="description" id="description" required>
                
                <label class="admin-element" for="duree">Durée (minutes)</label>
                <input class="admin-element" type="number" name="duree" id="duree" required>

                <label class="admin-element" for="url">URL vidéo</label>
                <input class="admin-element" type="url" name="url" id="url" required>

                <label class="admin-element" for="horaire">Horaire prévisionnel</label>
                <input class="admin-element" type="time" name="horaire" id="horaire" required>

                <label class="admin-element" for="style">Style</label>
                <input class="admin-element" type="text" name="style" id="style" required>

                <input class="admin-element" type="submit" value="Créer Spectacle">
            </form>
            </div>
            HTML;

            } else {
                $nom = filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
                $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
                $url = filter_var($_POST['url'] ?? '', FILTER_SANITIZE_URL);
                $horaire = filter_var($_POST['horaire'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
                $style = filter_var($_POST['style'] ?? 'Indefini', FILTER_SANITIZE_SPECIAL_CHARS);
                (int) $duree = filter_var($_POST['duree'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

                try {
                    $idSpectacle = NrvRepository::getInstance()->ajouterSpectacle($nom, $description, intval($duree), $url, $horaire, $style);
                    $res = "<p>Spectacle créé avec succès. ID du spectacle : " . $idSpectacle."</p>";
                } catch (\Exception $e) {
                    $res = "<p>Erreur lors de la création du spectacle : " . $e->getMessage() . "</p>";
                }
            }
        }catch(AuthnException $e){
            $res = "<p> Vous n'avez pas l'autorisation </p>";
        }
        $html = $header . $res;
        return $html;
    }
}