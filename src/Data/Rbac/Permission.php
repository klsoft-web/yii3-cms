<?php

namespace App\Data\Rbac;

final readonly class Permission
{
    const CREATE_POST = 'Create post';
    const UPDATE_POST = 'Update post';
    const UPDATE_ONLY_YOUR_POSTS = 'Update only your posts';
    const DELETE_POST = 'Delete post';
    const CREATE_PAGE = 'Create page';
    const UPDATE_PAGE = 'Update page';
    const UPDATE_ONLY_YOUR_PAGES = 'Update only your pages';
    const DELETE_PAGE = 'Delete page';
    const CREATE_CATEGORY = 'Create category';
    const UPDATE_CATEGORY = 'Update category';
    const UPDATE_ONLY_YOUR_CATEGORIES = 'Update only your categories';
    const DELETE_CATEGORY = 'Delete category';
    const CREATE_NAVIGATION = 'Create navigation';
    const UPDATE_NAVIGATION = 'Update navigation';
    const UPDATE_ONLY_YOUR_NAVIGATIONS = 'Update only your navigations';
    const DELETE_NAVIGATION = 'Delete navigation';
    const UPLOAD_IMAGE = 'Upload image';
    const UPLOAD_FILE = 'Upload file';
    const CREATE_USER = 'Create user';
    const UPDATE_USER = 'Update user';
    const DELETE_USER = 'Delete user';
    const CREATE_ROLE = 'Create role';
    const UPDATE_ROLE = 'Update role';
    const DELETE_ROLE = 'Delete role';
    const READ_LOG = 'Read log';
}
