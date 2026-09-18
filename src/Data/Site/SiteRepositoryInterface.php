<?php

namespace App\Data\Site;

interface SiteRepositoryInterface
{
    public function getHomePageSlug(): string;
}
