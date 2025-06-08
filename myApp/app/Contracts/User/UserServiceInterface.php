<?php
namespace App\Contracts\User;

interface UserServiceInterface {
    
    
    public function getUsers(int $perPage, ?string $search, ?string $sortBy = 'id', ?string $sortDirection = 'desc');
    

    public function getUser(int $id);
   

    public function createUser(array $data);
   

    public function updateUser(int $id, array $data);
    

    public function deleteUser(int $id): bool;
   

    
}
