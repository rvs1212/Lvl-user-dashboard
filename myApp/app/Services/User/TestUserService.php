<?php

namespace App\Services\User;

use App\Contracts\User\UserInterface;


class TestUserService implements UserInterface
{
    
    
   
    public function testServiceMethod(){
        return 'test api success!!';
    }
}
