<?php

namespace App\Data\User;

use App\Data\Entities\User;

interface CreatedByUserProviderInterface
{
    /**
     * @return User
     */
    public function getCreatedByUser(): User;
}
