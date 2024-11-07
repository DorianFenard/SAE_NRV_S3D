<?php
declare(strict_types=1);

namespace nrv\nancy\exception;

class AuthzException extends \Exception
{
    public function __construct(string $raison)
    {
        parent::__construct($raison);
    }
}