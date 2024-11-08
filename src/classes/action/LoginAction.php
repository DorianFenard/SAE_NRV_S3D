<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\exception\AuthnException;

class LoginAction extends Action
{
    /**
     * @throws AuthnException
     */
    public function execute(): string
    {
        $res = <<<HTML
        
HTML;
        if ($this->http_method === "GET") {
            $res = <<<HTML
                <div class="login-box">
                    <form class="login-form" method="post" action="?action=login">
                        <h1 class="login-text">CONNEXION</h1>
                        <label for="Mail"></label>
                        <input type="text" id="email" name="email" placeholder="<username>" required>
                        <label for="password"></label>
                        <input type="password" id="password" name="password" placeholder="<password>" required>
                        <input type="submit" value="Se connecter" id="submit-button">
                        <a class="login-home-button" href="?action=default">retourner à l'acceuil</a>
                    </form>
                </div>
            HTML;
        } else {
            if (isset($_SESSION['user'])) {
                unset($_SESSION['user']);
            }
            try {
                if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) throw new AuthnException("format incorrect");
                $email = $_POST['email'];
                $password = $_POST['password'];
                if (AuthnProvider::signin($email, $password)) {
                    $_SESSION['user'] = $email;
                    unset($_SESSION['']);
                    $res .= "<p>Connection approuvée</p>";
                }
            } catch
            (AuthnException $e) {
                $res = <<<HTML
                        <div class="login-error">
                            <h2 class="login-error-message">Informations incorrectes</h2>
                            <a class="error-home-button" href="?action=default">retourner à l'acceuil</a>
                        </div>
                    HTML;
            }
        }
        return $res;
    }
}