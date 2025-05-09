<?php
namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    //Full text index based seach can be done later
    public function paginateWithAddress(int $perPage, ?string $search): LengthAwarePaginator
    {
        return User::with('address')
            ->when($search, function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name',  'like', "%{$search}%")
                ->orWhere('email',      'like', "%{$search}%")
                ->orWhereHas('address', function ($q) use ($search) {
                    $q->where('city',    'like', "%{$search}%")
                        ->orWhere('country','like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }


    public function findWithAddressById(int $id): ?User
    {
        return User::with('address')->find($id);
    }


    public function createWithAddress(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name'        => $data['first_name'],
                'last_name'         => $data['last_name'],
                'email'             => $data['email'],
                'password'          => bcrypt($data['password']),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]);

            $user->address()->create([
                'country'   => $data['country'],
                'city'      => $data['city'],
                'post_code' => $data['post_code'],
                'street'    => $data['street'],
            ]);
            return $user->load('address');
        });
    }

    public function updateWithAddress(int $id, array $data): ?User
    {
        return DB::transaction(function () use ($id, $data) {
            $result = null;
            $user = User::find($id);
            if ($user) {
                $user->fill([
                    'first_name' => $data['first_name'],
                    'last_name'  => $data['last_name'],
                    'email'      => $data['email'],
                ]);

                if (! empty($data['password'])) {
                    $user->password = bcrypt($data['password']);
                }
                $user->save();

                $user->address()->updateOrCreate(
                    ['user_id' => $id],
                    ['country'   => $data['country'],
                    'city'      => $data['city'],
                    'post_code' => $data['post_code'],
                    'street'    => $data['street'],
                    ]
                );

                $result = $user->load('address');
            }
            return $result;
        });
    }

    public function deleteById(int $id): bool
    {
        // Wrap in transaction so address deletion (via FK cascade) is safe
        return DB::transaction(function () use ($id) {
            return User::where('id', $id)->delete();
        });
    }
}
