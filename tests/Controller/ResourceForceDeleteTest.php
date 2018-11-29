<?php

namespace Laravel\Nova\Tests\Controller;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\IdFilter;
use Laravel\Nova\Tests\Fixtures\UserPolicy;

class ResourceForceDeleteTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_can_force_delete_resources()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/users/force', [
                            'resources' => [$user->id, $user2->id],
                        ]);

        $response->assertStatus(200);

        $this->assertCount(0, User::all());

        $this->assertCount(2, ActionEvent::all());
        $this->assertEquals('Delete', ActionEvent::first()->name);
        $this->assertEquals($user->id, ActionEvent::where('actionable_id', $user->id)->first()->target_id);
    }

    public function test_can_force_delete_resources_via_search()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/users/force?search='.$user->email, [
                            'resources' => 'all',
                        ]);

        $response->assertStatus(200);

        $this->assertCount(1, User::all());

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Delete', ActionEvent::first()->name);
        $this->assertEquals($user->id, ActionEvent::where('actionable_id', $user->id)->first()->target_id);
    }

    public function test_can_destroy_resources_via_filters()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $filters = base64_encode(json_encode([
            [
                'class' => IdFilter::class,
                'value' => 1,
            ],
        ]));

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/users/force?filters='.$filters, [
                            'resources' => 'all',
                        ]);

        $response->assertStatus(200);

        $this->assertCount(1, User::all());

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Delete', ActionEvent::first()->name);
        $this->assertEquals($user->id, ActionEvent::where('actionable_id', $user->id)->first()->target_id);
    }

    public function test_cant_force_delete_resources_not_authorized_to_force_delete()
    {
        $user = factory(User::class)->create();

        $_SERVER['nova.user.authorizable'] = true;
        $_SERVER['nova.user.forceDeletable'] = false;

        Gate::policy(User::class, UserPolicy::class);

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/users/force', [
                            'resources' => [$user->id],
                        ]);

        unset($_SERVER['nova.user.authorizable']);
        unset($_SERVER['nova.user.forceDeletable']);

        $response->assertStatus(200);
        $this->assertCount(1, User::all());
        $this->assertCount(0, ActionEvent::all());
    }
}
