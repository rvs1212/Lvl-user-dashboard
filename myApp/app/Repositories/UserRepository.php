<?php
namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function paginateWithAddress(int $perPage, ?string $search): LengthAwarePaginator
    {
        return User::with('address')
            ->when($search, fn($q) => $q
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name',  'like', "%{$search}%")
                ->orWhere('email',      'like', "%{$search}%")
                ->orWhereHas('address', fn($q) => $q->where('city', 'like', "%{$search}%"))
            )
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function findWithAddressById(int $id): ?User
    {
        return User::with('address')->find($id);
    }
}
