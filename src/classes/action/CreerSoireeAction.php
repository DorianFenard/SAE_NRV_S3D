<?php
declare(strict_types=1);
namespace nrv\nancy\action;

use nrv\nancy\festival\Soiree;
use nrv\nancy\repository\NrvRepository;

class CreerSoireeAction extends Action{
    public function execute(): string
    {
        // TODO: Implement execute() method.
        if($this->http_method == "GET"){
            $res = <<<HTML
                <form action="?action=creerSoiree" method="post">
                    <input type="date" name="dateSoiree" required>
                    <input type="time" name="heureSoiree" required>
                    <input type="text" name="nomSoiree" placeholder="Nom soirée" required>
                    <intput type="text" name="themeSoiree" placeholder="Thématique" required>
                    <input type="text" name="descSoiree" placeholder="Description">
                    <input type="submit" value="Créer soirée" name="creerSoiree">
                </form>
                HTML;

        }else{
            $nomSoiree = filter_var($_POST["nomSoiree"], FILTER_SANITIZE_SPECIAL_CHARS);
            $themeSoiree = filter_var($_POST["themeSoiree"], FILTER_SANITIZE_SPECIAL_CHARS);
            $dateSoiree = filter_var($_POST["dateSoiree"], FILTER_SANITIZE_SPECIAL_CHARS);
            $heureSoiree = filter_var($_POST["heureSoiree"], FILTER_SANITIZE_SPECIAL_CHARS);

            NrvRepository::getInstance()->addSoiree($nomSoiree, $themeSoiree, $dateSoiree, $heureSoiree);
        }
        return $res;
    }
}