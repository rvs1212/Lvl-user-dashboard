<?php
namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserRepositoryInterface
{
    public function paginateWithAddress(int $perPage, ?string $search): LengthAwarePaginator;
    public function findWithAddressById(int $id): ?User;
}
