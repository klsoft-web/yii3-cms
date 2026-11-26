<?php

namespace App\Data\Auth;

use DateInterval;
use DateTimeImmutable;
use App\Data\Entities\User;
use Yiisoft\Security\Random;

final readonly class AuthKeyRepository implements AuthKeyRepositoryInterface
{
    /**
     * @param DateInterval|null $duration Interval until the auto-login cookie expires. If it isn't set it means
     * the auto-login cookie is session cookie that expires when browser is closed.
     */
    public function __construct(private ?DateInterval $duration = null)
    {
    }

    public function generate(): string
    {
        $timePart = '~' . time();
        return Random::string(User::AUTH_KEY_LENGTH - strlen($timePart)) . $timePart;
    }


    public function validate(string $key, string $originKey): bool
    {
        if ($key !== $originKey) {
            return false;
        }

        $parts = explode('~', $key);
        if (count($parts) !== 2) {
            return false;
        }

        if ($this->duration !== null) {
            return (new DateTimeImmutable())->sub($this->duration)->getTimestamp() < (int)$parts[1];
        }

        return true;
    }
}
