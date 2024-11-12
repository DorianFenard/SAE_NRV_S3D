<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\exception\AuthnException;
use nrv\nancy\repository\NrvRepository;

class CreerSpectacleAction extends Action {
    public function execute(): string {
        try{
            AuthnProvider::getSignedInUser();
            if ($this->http_method === "GET") {
                $res = <<<HTML
            <form action="?action=creerSpectacle" method="post">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required>

                <label for="description">Description</label>
                <input type="text" name="description" id="description" required>

                <label for="url">URL vidéo</label>
                <input type="url" name="url" id="url" required>

                <label for="horaire">Horaire prévisionnel</label>
                <input type="time" name="horaire" id="horaire" required>

                <label for="style">Style</label>
                <input type="text" name="style" id="style" required>

                <input type="submit" value="Créer Spectacle">
            </form>
            HTML;

            } else {
                $nom = filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
                $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
                $url = filter_var($_POST['url'] ?? '', FILTER_SANITIZE_URL);
                $horaire = filter_var($_POST['horaire'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
                $style = filter_var($_POST['style'] ?? 'Indefini', FILTER_SANITIZE_SPECIAL_CHARS);

                try {
                    $idSpectacle = NrvRepository::getInstance()->ajouterSpectacle($nom, $description, $url, $horaire, $style);
                    $res = "Spectacle créé avec succès. ID du spectacle : " . $idSpectacle;
                } catch (\Exception $e) {
                    $res = "Erreur lors de la création du spectacle : " . $e->getMessage();
                }
            }
        }catch(AuthnException $e){
            $res = "<p> Vous n'avez pas l'autorisation </p>";
        }
        return $res;
    }
}