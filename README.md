# YII3-CMS

It is a content management system based on the [Yii 3 framework](https://yii3.yiiframework.com) and uses the [Doctrine ORM](https://www.doctrine-project.org/).

## Features

- Create and manage pages, posts, and navigation.
- Design SEO-friendly URLs.
- Manage permissions.

## Requirements

- PHP 8.2 - 8.5.

## How to use

 1. Create a new project from a template using the [Composer](https://getcomposer.org/) package manager:

```bash
composer create-project klsoft/yii3-cms my_site
cd my_site
```

2. Configure the Doctrine connection in the `config/common/params.php`.

3. Run the init console command:

```bash
APP_ENV=dev ./yii init
```

4. To run the app:

```bash
APP_ENV=dev ./yii serve --port=8383
```
Open your browser to the URL [http://localhost:8383](http://localhost:8383)

## The following the Doctrine console commands are currently available:

- doctrine:orm:schema-tool:create
- doctrine:orm:schema-tool:drop
- doctrine:orm:schema-tool:update
- doctrine:orm:clear-cache:metadata
- doctrine:orm:validate-schema
- doctrine:orm:mapping-describe
- doctrine:orm:run-dql
- doctrine:orm:info
- doctrine:orm:generate-proxies
- doctrine:orm:clear-cache:query
- doctrine:orm:clear-cache:result
- doctrine:dbal:run-sql
