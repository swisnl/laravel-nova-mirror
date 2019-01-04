<?php

namespace Laravel\Nova\Tests\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Tests\Fixtures\Role;
use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\IdFilter;
use Laravel\Nova\Tests\Fixtures\UserPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;

class ResourceDestroyTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_can_destroy_resources()
    {
        $role = factory(Role::class)->create();
        $user = factory(User::class)->create();
        $role->users()->attach($user);
        $role2 = factory(Role::class)->create();

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/roles', [
                            'resources' => [$role->id, $role2->id],
                        ]);

        $response->assertStatus(200);

        $this->assertCount(0, Role::all());
        $this->assertCount(1, DB::table('user_roles')->get());

        $this->assertCount(2, ActionEvent::all());
        $this->assertEquals('Delete', ActionEvent::first()->name);
        $this->assertEquals($role->id, ActionEvent::where('actionable_id', $role->id)->first()->target_id);
    }

    public function test_destroying_resource_can_prune_attachment_records()
    {
        $_SERVER['__nova.role.prunable'] = true;

        $role = factory(Role::class)->create();
        $user = factory(User::class)->create();
        $role->users()->attach($user);

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/roles', [
                            'resources' => [$role->id],
                        ]);

        unset($_SERVER['__nova.role.prunable']);

        $response->assertStatus(200);

        $this->assertCount(0, Role::all());
        $this->assertCount(0, DB::table('user_roles')->get());
    }

    public function test_can_destroy_resources_via_search()
    {
        $role = factory(Role::class)->create();
        $role2 = factory(Role::class)->create();

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/roles?search=1', [
                            'resources' => 'all',
                        ]);

        $response->assertStatus(200);

        $this->assertCount(1, Role::all());

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Delete', ActionEvent::first()->name);
        $this->assertEquals($role->id, ActionEvent::where('actionable_id', $role->id)->first()->target_id);
    }

    public function test_can_destroy_resources_via_filters()
    {
        $role = factory(Role::class)->create();
        $role2 = factory(Role::class)->create();

        $filters = base64_encode(json_encode([
            [
                'class' => IdFilter::class,
                'value' => 1,
            ],
        ]));

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/roles?filters='.$filters, [
                            'resources' => 'all',
                        ]);

        $response->assertStatus(200);

        $this->assertCount(1, Role::all());

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Delete', ActionEvent::first()->name);
        $this->assertEquals($role->id, ActionEvent::where('actionable_id', $role->id)->first()->target_id);
    }

    public function test_can_destroy_soft_deleted_resources()
    {
        $user = factory(User::class)->create();
        $this->assertNull($user->deleted_at);

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/users', [
                            'resources' => [$user->id],
                        ]);

        $response->assertStatus(200);

        $user = $user->fresh();
        $this->assertNotNull($user->deleted_at);

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Delete', ActionEvent::first()->name);
        $this->assertEquals($user->id, ActionEvent::first()->target->id);
        $this->assertTrue($user->is(ActionEvent::first()->target));
    }

    public function test_cant_destroy_resources_not_authorized_to_destroy()
    {
        $user = factory(User::class)->create();
        $this->assertNull($user->deleted_at);

        $_SERVER['nova.user.authorizable'] = true;
        $_SERVER['nova.user.deletable'] = false;

        Gate::policy(User::class, UserPolicy::class);

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/users', [
                            'resources' => [$user->id],
                        ]);

        unset($_SERVER['nova.user.authorizable']);
        unset($_SERVER['nova.user.deletable']);

        $response->assertStatus(200);

        $user = $user->fresh();
        $this->assertNull($user->deleted_at);

        $this->assertCount(0, ActionEvent::all());
    }

    public function test_action_event_should_honor_custom_polymorphic_type_for_soft_deletions()
    {
        Relation::morphMap(['role' => Role::class]);

        $role = factory(Role::class)->create();
        $user = factory(User::class)->create();
        $role->users()->attach($user);

        $response = $this->withExceptionHandling()
                        ->deleteJson('/nova-api/roles', [
                            'resources' => [$role->id],
                        ]);

        $actionEvent = ActionEvent::first();

        $this->assertEquals('Delete', $actionEvent->name);

        $this->assertEquals('role', $actionEvent->actionable_type);
        $this->assertEquals($role->id, $actionEvent->actionable_id);

        $this->assertEquals('role', $actionEvent->target_type);
        $this->assertEquals($role->id, $actionEvent->target_id);

        $this->assertEquals('role', $actionEvent->model_type);
        $this->assertEquals($role->id, $actionEvent->model_id);

        Relation::morphMap([], false);
    }
}
