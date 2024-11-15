<?php
declare(strict_types=1);

namespace nrv\nancy\action;

use nrv\nancy\auth\AuthnProvider;
use nrv\nancy\exception\AuthnException;
use nrv\nancy\repository\NrvRepository;

/**
 * Action déclenchée lorsque l'on veut se connecter,
 * si $http_method != "GET" on affiche le formulaire, sinon on l'exécute
 */
class LoginAction extends Action
{
    /**
     * @throws AuthnException
     */
    public function execute(): string
    {
        $loginButton = isset($_SESSION['user'])
            ? '<a class="login-button" href="?action=logout">SE DÉCONNECTER</a>'
            : '<a class="login-button" href="?action=login">SE CONNECTER</a>';
        $adminButton = isset($_SESSION['role']) && $_SESSION['role'] >= 50
            ? '<a class="admin-button" href="?action=adminpage">ADMIN</a>'
            : '';
        $header = '<header class="program-header"><a class="home" href="?action=default">
                        <img class="program-icon" src="./images/icone.png" alt="NRV">
                    </a> <div class="menu">
                        <a class="list-button" href="?action=">Acceuil</a>
                        <a class="list-button" href="?action=list">MA LISTE</a>
                        <a class="program-button" href="?action=program">PROGRAMME</a>'.
            $adminButton.
            $loginButton .'              
                    </div>
                    </header>
                    <div class="filters">';
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
                        <a class="login-home-button" href="?action=default">retourner à l'accueil</a>
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

                    $pdo = NrvRepository::getInstance();
                    $role = intval($pdo->getRoleByUser($email));

                    if ($role === 100) {
                        $_SESSION['role'] = 100;
                    } else {
                        $_SESSION['role'] = $role;
                    }

                    header("Location: index.php?action=default");
                    exit;
                }
            } catch (AuthnException $e) {
                $res = <<<HTML
                        <div class="login-error">
                            <h2 class="login-error-message">Informations incorrectes</h2>
                            <a class="error-home-button" href="?action=default">retourner à l'accueil</a>
                        </div>
                    HTML;
            }
        }
        return $header . $res;
    }
}