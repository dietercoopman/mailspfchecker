<?php

namespace Dietercoopman\Mailspfchecker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dietercoopman\Mailspfchecker\Mailspfchecker
 */
class Mailspfchecker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Dietercoopman\Mailspfchecker\Mailspfchecker::class;
    }
}
