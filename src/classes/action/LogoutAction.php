<?php
declare(strict_types=1);
namespace nrv\nancy\action;

class LogoutAction
{
    public function execute(): string
    {
        session_start();
        session_destroy();
        header('Location: ?action=default');
        return "";
    }

}