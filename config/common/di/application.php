<?php

declare(strict_types=1);

use App\Shared\ApplicationParams;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Klsoft\Yii3CacheDoctrine\DoctrineCache;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Definitions\Reference;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;

/** @var array $params */

return [
    ApplicationParams::class => [
        '__construct()' => [
            'name' => $params['application']['name'],
            'charset' => $params['application']['charset'],
            'locale' => $params['application']['locale'],
        ],
    ],

    CacheInterface::class => static function (ContainerInterface $container) {
        return new Cache(new DoctrineCache($container->get(EntityManagerInterface::class)));
    },

    CacheItemPoolInterface::class => ArrayAdapter::class, //One of the following adapters should be used instead: Psr16Adapter, RedisAdapter, MemcachedAdapter, DoctrineDbalAdapter, and so forth.

    Configuration::class => static function (ContainerInterface $container) use ($params) {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $params['doctrine']['paths'],
            isDevMode: $params['doctrine']['isDevMode'],
            cache: $container->get(CacheItemPoolInterface::class));
        $config->setAutoGenerateProxyClasses(ProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS_OR_CHANGED);
        return $config;
    },

    EntityManagerInterface::class => static function (ContainerInterface $container) use ($params) {
        $configuration = $container->get(Configuration::class);
        return new EntityManager(
            DriverManager::getConnection(
                $params['doctrine']['connection'],
                $configuration
            ),
            $configuration);
    },
    EntityManagerProvider::class => SingleManagerProvider::class,

    MessageFormatterInterface::class => SimpleMessageFormatter::class,

    TranslatorInterface::class => [
        'class' => Translator::class,
        '__construct()' => [
            'locale' => $params['application']['locale'],
            'fallbackLocale' => $params['yiisoft/translator']['fallbackLocale'],
            'defaultCategory' => $params['yiisoft/translator']['defaultCategory'],
            'defaultMessageFormatter ' => Reference::to(MessageFormatterInterface::class),
        ],
        'addCategorySources()' => ['categories' => [
            new CategorySource($params['yiisoft/translator']['defaultCategory'], new MessageSource($params['messagesPath']))
        ]],
    ],
];
