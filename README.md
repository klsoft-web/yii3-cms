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
3. Create and initialise the database schema:
```bash
./yii init
```
4. To run the application:
```bash
./yii serve
```
To run the application using the specified options:
```bash
./yii serve --address=127.0.0.1 --port=8080
```

Open your browser to the URL [http://localhost:8080](http://localhost:8080)

If you would prefer to use a different web server, please refer to the [Configuring web servers](https://yiisoft.github.io/docs/cookbook/configuring-webservers/general.html) section.
To achieve the best performance, use the Swoole HTTP server via the [klsoft-web/yii3-swoole](https://github.com/klsoft-web/yii3-swoole) package.

## How to use with Docker

 1. Clone the repository.
 2. Uncomment the line `'host' => 'mysql'` in the `config/common/params.php`
 3. Start the application: 
    ```bash 
    docker compose up -d 
    ```
 4. Create and initialise the database schema:
    ```bash 
    docker compose exec app ./yii init 
    ```

Open your browser to the URL [http://localhost:8080](http://localhost:8080)

## The following the Doctrine console commands are currently available:

- doctrine:orm:schema-tool:create
- doctrine:orm:schema-tool:drop
- doctrine:orm:schema-tool:update
- doctrine:orm:validate-schema
- doctrine:orm:mapping-describe
- doctrine:orm:generate-proxies
- doctrine:orm:run-dql
- doctrine:orm:info
- doctrine:orm:clear-cache:metadata
- doctrine:orm:clear-cache:query
- doctrine:orm:clear-cache:result
- doctrine:dbal:run-sql

Display help for a command:
```bash
./yii <command> --help
```
