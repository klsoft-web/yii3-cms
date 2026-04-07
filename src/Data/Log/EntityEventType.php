<?php

namespace App\Data\Log;

enum EntityEventType: string
{
    case Insert = 'Insert';
    case Update = 'Update';
    case Delete = 'Delete';
}

