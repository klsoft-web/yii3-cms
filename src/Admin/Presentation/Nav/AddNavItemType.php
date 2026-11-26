<?php

namespace App\Admin\Presentation\Nav;

enum AddNavItemType: string
{
    case Page = 'Page';
    case Category = 'Category';
    case Url = 'Url';
}
