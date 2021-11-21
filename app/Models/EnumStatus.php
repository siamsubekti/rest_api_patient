<?php

namespace App\Models;

class EnumStatus
{
    const POSITIVE = 'positive';
    const RECOVERY = 'recovered';
    const DEAD = 'dead';
    public static $status = [self::POSITIVE, self::DEAD, self::RECOVERY];
}
