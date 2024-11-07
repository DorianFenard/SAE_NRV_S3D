<?php
declare(strict_types=1);
namespace nrv\nancy\action;

class SigninAction extends Action{
    public function execute(): string
    {
        if($this->http_method === "GET"){
            $res = <<<HTML
            <form method="post" action="?action=signin">
                <label for="Mail">Adresse mail</label>
                <input type="text" id="email" name="email" placeholder="<username>" required>
                <label for="password">Votre password</label>
                <input type="password" id="password" name="password" placeholder="<password>" required>
                <input type="submit" value="Se connecter" id="submit-button">
            </form>
            HTML;
        }else{

            //TODO
            //Traiter les données
            //Renvoyé le message si auth sinon erreur
            $res = "authentifié";
        }

        return $res;
    }
}