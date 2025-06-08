<?php
namespace App\Services\User;

use App\Contracts\User\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function __construct(protected UserRepositoryInterface $users) {

    }

    public function getUsers(int $perPage, ?string $search, ?string $sortBy = 'id', ?string $sortDirection = 'desc'): LengthAwarePaginator
    {
        return $this->users->paginateWithAddress($perPage, $search, $sortBy, $sortDirection);
    }

    public function getUser(int $id): ?User
    {
        return $this->users->findWithAddressById($id);
    }

    public function createUser(array $data): User
    {
        return $this->users->createWithAddress($data);
    }

    public function updateUser(int $id, array $data): ?User
    {
        return $this->users->updateWithAddress($id, $data);
    }

    public function deleteUser(int $id): bool
    {
        return $this->users->deleteById($id);
    }
}
