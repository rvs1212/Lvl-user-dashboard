<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\ShowUserRequest;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService){
 
    }



    public function index(IndexUserRequest $request)
    {
        $params  = $request->validated();
        $perPage = $params['per_page'] ?? 15;
        $search  = $params['search']   ?? null;

        return response()->json(
            $this->userService->getUsers($perPage, $search)
        );
    }

    public function show(ShowUserRequest $request)
    {
        $id = $request->validated()['id'];

        $user = $this->userService->getUser($id);

        return $user
            ? response()->json($user)
            : response()->json(['message' => 'Not found'], 404);
    }
}
