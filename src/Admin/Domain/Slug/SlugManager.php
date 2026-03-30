<?php

namespace App\Admin\Domain\Slug;

use App\Data\Entities\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\UnicodeString;
use Yiisoft\Strings\Inflector;

final readonly class SlugManager implements SlugManagerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private int                    $numberOfAttempts = 10)
    {
    }

    public function create(string $text): string
    {
        $slug = substr((new Inflector())->toSlug((new UnicodeString($text))->ascii()), 0, Slug::ID_LENGTH);
        $tempSlug = $slug;
        for ($i = 1; $i <= $this->numberOfAttempts; $i++) {
            try {
                if ($this->entityManager->find(Slug::class, $tempSlug) === null) {
                    break;
                } else {
                    $addedText = '-' . $i;
                    $tempSlug = substr($slug, 0, Slug::ID_LENGTH - strlen($addedText)) . $addedText;
                }
            } catch (Throwable) {
                break;
            }
        }

        return $tempSlug;
    }
}
