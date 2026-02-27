<?php

namespace Ifirma;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class IfirmaBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}