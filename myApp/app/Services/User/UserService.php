<?php
namespace App\Services\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

class UserService
{
    public function __construct(protected UserRepositoryInterface $users) {

    }

    public function getUsers(int $perPage, ?string $search): LengthAwarePaginator
    {
        return $this->users->paginateWithAddress($perPage, $search);
    }

    public function getUser(int $id): ?User
    {
        return $this->users->findWithAddressById($id);
    }
}
