<?php

namespace Bab\SatisApi;

class ExceptionFactory
{
    public static function invalidRepository(string $url): Exception
    {
        return new Exception("Invalid repository $url.");
    }
}
