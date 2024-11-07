<?php
declare(strict_types=1);

namespace nrv\nancy\exception;

class AuthnException extends \Exception
{
    public function __construct(string $reason)
    {
        parent::__construct($reason);
    }
}