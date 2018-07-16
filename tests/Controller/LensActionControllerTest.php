<?php

namespace Laravel\Nova\Tests\Controller;

use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\NoopAction;

class LensActionControllerTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_lens_actions_can_be_applied()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->post('/nova-api/users/lens/user-lens/action?action='.(new NoopAction)->uriKey(), [
                            'resources' => implode(',', [$user->id, $user2->id]),
                            'test' => 'Taylor Otwell',
                        ]);

        $response->assertStatus(200);
        $this->assertEquals(['message' => 'Hello World'], $response->original);
    }

    public function test_lens_actions_can_be_applied_to_entire_lens()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->post('/nova-api/users/lens/user-lens/action?action='.(new NoopAction)->uriKey(), [
                            'resources' => 'all',
                            'test' => 'Taylor Otwell',
                        ]);

        $response->assertStatus(200);
        $this->assertEquals('Taylor Otwell', NoopAction::$appliedFields[0]->test);
    }

    /**
     * @expectedException LogicException
     */
    public function test_lens_actions_cant_be_applied_to_entire_lens_if_lens_returns_resource()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $response = $this->withoutExceptionHandling()
                        ->post('/nova-api/users/lens/paginating-user-lens/action?action='.(new NoopAction)->uriKey(), [
                            'resources' => 'all',
                        ]);
    }
}
