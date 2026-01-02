<?php

namespace App\Actions\Fortify;

use App\Domain\Authentication\Actions\RegisterAction;
use App\Domain\Authentication\DTOs\RegisterData;
use Laravel\Fortify\Contracts\CreatesNewUsers;

/**
 * Fortify Adapter - 桥接 Fortify 和 DDD Architecture
 */
class CreateNewUserAdapter implements CreatesNewUsers
{
    public function __construct(
        private RegisterAction $registerAction
    ) {}

    public function create(array $input)
    {
        $data = RegisterData::from($input);
        return $this->registerAction->execute($data);
    }
}
