<?php
declare(strict_types=1);
namespace nrv\nancy\action;

class LoginAction extends Action{
    public function execute(): string
    {
        if($this->http_method === "GET"){
            $res = <<<HTML
            <div class="login-background">
                <div class="login-box">
                    <form class="login-form" method="post" action="?action=signin">
                        <label for="Mail"></label>
                        <input type="text" id="email" name="email" placeholder="<username>" required>
                        <label for="password"></label>
                        <input type="password" id="password" name="password" placeholder="<password>" required>
                        <input type="submit" value="Se connecter" id="submit-button">
                        <a class="signup-button" href="?action=signup">Créer un compte</a>
                    </form>
                </div>
            </div>
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