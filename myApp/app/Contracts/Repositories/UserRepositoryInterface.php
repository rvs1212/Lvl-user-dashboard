<?php
namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserRepositoryInterface
{
    public function paginateWithAddress(int $perPage, ?string $search, ?string $sortBy = 'id', ?string $sortDirection = 'desc'): LengthAwarePaginator;
    public function findWithAddressById(int $id): ?User;
    public function createWithAddress(array $data): User;
    public function updateWithAddress(int $id, array $data): ?User;
    public function deleteById(int $id): bool;
}
