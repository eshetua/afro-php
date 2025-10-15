<?php

namespace Afromessage\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class AfroMessage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Afromessage\Laravel\Contracts\AfroMessageInterface::class;
    }
}