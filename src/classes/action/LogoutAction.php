<?php
declare(strict_types=1);
namespace nrv\nancy\action;

class LogoutAction
{
    public function execute(): string
    {
        session_destroy();
        session_start();
        header('Location: ?action=default');
        return "";
    }

}