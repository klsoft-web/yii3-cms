<?php

namespace App\Data\Post;

enum PostStatus: string
{
    case Inactive = 'Inactive';
    case Active = 'Active';
}
