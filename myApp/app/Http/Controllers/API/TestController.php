<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\User\UserInterface;


class TestController extends Controller
{
    protected $userService;
    public function __construct(UserInterface $userService) {
        $this->userService = $userService;
    }


    public function testMethod(){
        
        $result = $this->userService->testServiceMethod();
        return response()->json([
            'status'=> $result,
            'code'=> 200
        ]);
    }

}
