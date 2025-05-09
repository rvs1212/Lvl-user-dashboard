<?php

namespace App\Http\Controllers\Api;

use App\Contracts\User\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\ShowUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserServiceInterface $userService){
        
    }

    public function index(IndexUserRequest $request)
    {
        $params  = $request->validated();
        $perPage = $params['per_page'] ?? config('data.pagination.per_page_default');
        $search  = $params['search']   ?? null;

        return response()->json(
            $this->userService->getUsers($perPage, $search),
            config('data.http.ok')
        );
    }

    public function show(ShowUserRequest $request)
    {
        $id = $request->validated()['id'];
        $user = $this->userService->getUser($id);

        return $user
                ? response()->json($user, config('data.http.ok'))
                : response()->json(['message' => 'Not found'], config('data.http.not_found'));
    }

    public function store(CreateUserRequest $request){
        
        $data = $request->validated();
        $user = $this->userService->createUser($data);
        return response()->json($user, config('data.http.created'));
    }

    public function updateUser(UpdateUserRequest $request)
    {
        $data = $request->validated();
        // $data now includes: id, first_name, last_name, email,
        // password (nullable), country, city, post_code, street
        $user = $this->userService->updateUser($data['id'], $data);

        return response()->json($user, config('data.http.ok'));
    }


    public function deleteUser(int $id){
        $response = $this->userService->deleteUser($id);
        $result = response()->json(['message' => 'Not found'], config('data.http.not_found'));
        if ($response) {
            // 204 No Content is conventional for deletes
            $result = response()->json(null, config('data.http.no_content'));
        }
        return $result;
    }
}
