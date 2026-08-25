<?php

namespace App\Data\Site;

final readonly class SiteRepository implements SiteRepositoryInterface
{
    public function __construct(private string $homePageSlug)
    {
    }

    public function getHomePageSlug(): string
    {
        return $this->homePageSlug;
    }
}
