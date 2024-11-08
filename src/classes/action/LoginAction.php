<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\exception\AuthnException;

class LoginAction extends Action
{
    public function execute(): string
    {
        if ($this->http_method === "GET") {
            $res = <<<HTML
            <body class="login-background">
                <div class="login-box">
                    <form class="login-form" method="post" action="?action=signin">
                        <h1 class="login-text">CONNEXION</h1>
                        <label for="Mail"></label>
                        <input type="text" id="email" name="email" placeholder="<username>" required>
                        <label for="password"></label>
                        <input type="password" id="password" name="password" placeholder="<password>" required>
                        <input type="submit" value="Se connecter" id="submit-button">
                        <a class="login-home-button" href="?action=default">retourner à l'acceuil</a>
                    </form>
                </div>
            </body>
            HTML;
        } else {
            if (isset($_SESSION['user'])) {
                unset($_SESSION['user']);
            }
            $email = $_POST['email'];
            $password = $_POST['password'];
            try {
                if (AuthnProvider::signin($email, $password)) {
                    $_SESSION['user'] = $email;
                    unset($_SESSION['playlist']);
                    $res = "<p> Vous vous êtes bien identifié : " . $email . " , bienvenu </p>";
                }
            } catch (AuthnException $e) {
                $res = "<p> Vos identifiants sont incorrects </p>";
            }

        }
        return $res . '<a href="?action=default">Retourner a l\'acceuil</a>';
    }
}