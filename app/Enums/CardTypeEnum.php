<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static New()
 * @method static static Existing()
 */
final class CardTypeEnum extends Enum
{
    const NEW =   "NEW";
    const EXISTING =   "EXISTING";
}
