<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\User\UserService;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

class UserServiceTest extends TestCase
{
    protected $repo;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo    = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UserService($this->repo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_a_paginator_when_no_search_term_is_provided()
    {
        $perPage  = 15;
        $expected = new LengthAwarePaginator([], 0, $perPage);

        $this->repo
             ->shouldReceive('paginateWithAddress')
             ->once()
             ->with($perPage, null)
             ->andReturn($expected);

        $result = $this->service->getUsers($perPage, null);

        $this->assertSame($expected, $result);
    }

    public function test_it_passes_the_search_term_to_paginateWithAddress()
    {
        $perPage  = 10;
        $search   = 'alice';
        $expected = new LengthAwarePaginator([], 0, $perPage);

        $this->repo
             ->shouldReceive('paginateWithAddress')
             ->once()
             ->with($perPage, $search)
             ->andReturn($expected);

        $result = $this->service->getUsers($perPage, $search);

        $this->assertSame($expected, $result);
    }


    public function test_get_user_returns_user_when_found()
    {
        $id   = 42;
        $user = new User(['id' => $id]);
        
        $this->repo
            ->shouldReceive('findWithAddressById')
            ->once()
            ->with($id)
            ->andReturn($user);

        $result = $this->service->getUser($id);

        $this->assertSame($user, $result);
    }

    public function test_create_user_delegates_to_repository()
    {
        $data = [
            'first_name' => 'Bob',
            'last_name'  => 'Builder',
            'email'      => 'bob@builder.com',
            'password'   => '12345678',
            'city'       => 'New York',
            'country'    => 'United States of America',
            'post_code'  => '10001',
            'street'     => '123 Main St',
        ];

        $user = new User($data);

        $this->repo
            ->shouldReceive('createWithAddress')
            ->once()
            ->with($data)
            ->andReturn($user);

        $result = $this->service->createUser($data);

        $this->assertSame($user, $result);
    }

    public function test_update_user_delegates_to_repository()
    {
        $id   = 7;
        $data = [
            'first_name' => 'Carol',
            'last_name'  => 'Contractor',
            'email'      => 'carol@contractor.com',
            'password'   => '12345678',
            'city'       => 'New York',
            'country'    => 'United States of America',
            'post_code'  => '10001',
            'street'     => '123 Main St',
        ];

        $user = new User(array_merge(['id' => $id], $data));

        $this->repo
            ->shouldReceive('updateWithAddress')
            ->once()
            ->with($id, $data)
            ->andReturn($user);

        $result = $this->service->updateUser($id, $data);

        $this->assertSame($user, $result);
    }

    public function test_delete_user_delegates_to_repository()
    {
        $id = 99;
        $this->repo
            ->shouldReceive('deleteById')
            ->once()
            ->with($id)
            ->andReturnTrue();

        $result = $this->service->deleteUser($id);

        $this->assertTrue($result);
    }

}
