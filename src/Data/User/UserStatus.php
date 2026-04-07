<?php

namespace App\Data\User;

enum UserStatus: string
{
    case Inactive = 'Inactive';
    case Active = 'Active';
}
