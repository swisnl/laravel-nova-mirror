<?php

namespace Laravel\Nova\Tests\Controller;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\IdFilter;
use Laravel\Nova\Tests\Fixtures\UserPolicy;

class LensResourceDestroyTest extends IntegrationTest
{
    public function setUp() : void
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_can_destroy_resources()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/users/lens/user-lens', [
                            'resources' => [$user->id, $user2->id],
                        ]);

        $response->assertStatus(200);

        $this->assertCount(0, User::all());
        $this->assertCount(2, User::withTrashed()->get());

        $this->assertCount(2, ActionEvent::all());
        $this->assertEquals('Delete', ActionEvent::first()->name);
        $this->assertEquals($user->id, ActionEvent::where('actionable_id', $user->id)->first()->target_id);
    }

    public function test_can_destroy_all_matching_resources()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/users/lens/user-lens', [
                            'resources' => 'all',
                        ]);

        $response->assertStatus(200);

        $this->assertCount(0, User::all());
        $this->assertCount(2, User::withTrashed()->get());

        $this->assertCount(2, ActionEvent::all());
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
                        ->deleteJson('/nova-api/users/lens/user-lens?filters='.$filters, [
                            'resources' => 'all',
                        ]);

        $response->assertStatus(200);

        $this->assertCount(1, User::all());

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Delete', ActionEvent::first()->name);
        $this->assertEquals($user->id, ActionEvent::where('actionable_id', $user->id)->first()->target_id);
    }

    public function test_cant_destroy_resources_not_authorized_to_destroy()
    {
        $user = factory(User::class)->create();
        $this->assertNull($user->deleted_at);

        $_SERVER['nova.user.authorizable'] = true;
        $_SERVER['nova.user.deletable'] = false;

        Gate::policy(User::class, UserPolicy::class);

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/users/lens/user-lens', [
                            'resources' => [$user->id],
                        ]);

        unset($_SERVER['nova.user.authorizable']);
        unset($_SERVER['nova.user.deletable']);

        $response->assertStatus(200);

        $user = $user->fresh();
        $this->assertNull($user->deleted_at);

        $this->assertCount(0, ActionEvent::all());
    }
}
