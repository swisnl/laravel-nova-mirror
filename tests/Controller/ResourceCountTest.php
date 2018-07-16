<?php

namespace Laravel\Nova\Tests\Controller;

use Laravel\Nova\Nova;
use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\IdFilter;

class ResourceCountTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_can_count_a_resource()
    {
        factory(User::class)->create();
        factory(User::class)->create();
        factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->getJson('/nova-api/users/count');

        $response->assertStatus(200);
        $this->assertEquals(3, $response->original['count']);
    }

    public function test_can_count_a_resource_via_search()
    {
        factory(User::class)->create();
        factory(User::class)->create();
        factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->getJson('/nova-api/users/count?search=1');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->original['count']);
    }

    public function test_can_count_a_resource_via_filter()
    {
        factory(User::class)->create();
        factory(User::class)->create();
        factory(User::class)->create();

        $filters = base64_encode(json_encode([
            [
                'class' => IdFilter::class,
                'value' => 2,
            ],
        ]));

        $response = $this->withExceptionHandling()
                        ->getJson('/nova-api/users/count?filters='.$filters);

        $response->assertStatus(200);
        $this->assertEquals(1, $response->original['count']);
    }
}
