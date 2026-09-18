<?php

namespace App\Data\Id;

interface IdProviderInterface
{
    public function getIdAsString(): ?string;
}
