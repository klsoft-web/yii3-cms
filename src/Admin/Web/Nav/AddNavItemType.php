<?php

namespace App\Admin\Web\Nav;

enum AddNavItemType: string
{
    case Page = 'Page';
    case Category = 'Category';
    case Url = 'Url';
}
