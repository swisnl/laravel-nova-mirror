<?php

namespace Laravel\Nova\Tests\Controller;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Tests\Fixtures\Post;
use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\Address;
use Laravel\Nova\Tests\Fixtures\CustomKey;
use Laravel\Nova\Tests\Fixtures\UserPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;

class ResourceCreationTest extends IntegrationTest
{
    public function setUp() : void
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_can_create_resources()
    {
        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users', [
                            'name' => 'Taylor Otwell',
                            'email' => 'taylor@laravel.com',
                            'password' => 'secret',
                        ]);

        $response->assertStatus(201);

        $user = User::first();
        $this->assertEquals('Taylor Otwell', $user->name);
        $this->assertEquals('taylor@laravel.com', $user->email);

        $actionEvent = ActionEvent::first();
        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Create', $actionEvent->name);
        $this->assertEquals($user->id, $actionEvent->target->id);
        $this->assertTrue($user->is($actionEvent->target));
    }

    public function test_can_return_custom_pk()
    {
        $response = $this->withExceptionHandling()
            ->postJson('/nova-api/custom-keys', [
            ]);

        $response->assertStatus(201);

        $model = CustomKey::first();

        $this->assertEquals($model->pk, $response->getData()->id);
    }

    public function test_can_create_resources_with_null_relation()
    {
        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/posts', [
                            'title' => 'Test Post',
                            'user' => '',
                        ]);

        $response->assertStatus(201);

        $post = Post::first();

        $this->assertNull($post->user_id);
    }

    public function test_can_create_resource_fields_that_arent_authorized()
    {
        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users', [
                            'name' => 'Taylor Otwell',
                            'email' => 'taylor@laravel.com',
                            'password' => 'secret',
                            'restricted' => 'No',
                        ]);

        $response->assertStatus(201);

        $user = User::first();
        $this->assertEquals('Taylor Otwell', $user->name);
        $this->assertEquals('taylor@laravel.com', $user->email);
        $this->assertEquals('Yes', $user->restricted);
    }

    public function test_must_be_authorized_to_create_resource()
    {
        $_SERVER['nova.user.authorizable'] = true;
        $_SERVER['nova.user.creatable'] = false;

        Gate::policy(User::class, UserPolicy::class);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users', [
                            'name' => 'Taylor Otwell',
                            'email' => 'taylor@laravel.com',
                            'password' => 'secret',
                        ]);

        unset($_SERVER['nova.user.authorizable']);
        unset($_SERVER['nova.user.creatable']);

        $response->assertStatus(403);
    }

    public function test_validation_rules_are_applied()
    {
        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users', [
                            'password' => '',
                        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'email',
            'password',
        ]);

        $user = User::first();
        $this->assertNull($user);
    }

    public function test_resource_with_parent_can_be_created()
    {
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/posts', [
                            'user' => $user->id,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(201);
    }

    public function test_must_be_authorized_to_relate_related_resource_to_create_a_resource_that_it_belongs_to()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/posts', [
                            'user' => $user3->id,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(422);

        // Ensure base User::relatableQuery was called...
        $this->assertFalse(isset($_SERVER['nova.post.relatableUsers']));
    }

    public function test_resource_may_specify_custom_relatable_query_customizer()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();

        $_SERVER['nova.post.useCustomRelatableUsers'] = true;
        unset($_SERVER['nova.post.relatableUsers']);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/posts', [
                            'user' => $user3->id,
                            'title' => 'Fake Title',
                        ]);

        unset($_SERVER['nova.post.useCustomRelatableUsers']);

        $this->assertNotNull($_SERVER['nova.post.relatableUsers']);
        $response->assertStatus(422);

        unset($_SERVER['nova.post.relatableUsers']);
    }

    public function test_parent_resource_policy_may_prevent_adding_related_resources()
    {
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/posts', [
                            'user' => $user->id,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(201);

        $_SERVER['nova.user.authorizable'] = true;
        $_SERVER['nova.user.addPost'] = false;

        Gate::policy(User::class, UserPolicy::class);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/posts', [
                            'user' => $user->id,
                            'title' => 'Fake Title',
                        ]);

        unset($_SERVER['nova.user.authorizable']);
        unset($_SERVER['nova.user.addPost']);

        $response->assertStatus(422);
        $this->assertInstanceOf(User::class, $_SERVER['nova.user.addPostModel']);
        $this->assertEquals($user->id, $_SERVER['nova.user.addPostModel']->id);

        unset($_SERVER['nova.user.addPostModel']);
    }

    public function test_parent_resource_must_exist()
    {
        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/posts', [
                            'user' => 100,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user']);
    }

    public function test_can_create_resource_via_parent_resource()
    {
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/posts?viaResource=users&viaResourceId=1&viaRelationship=posts', [
                            'user' => $user->id,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(201);
    }

    public function test_related_resource_must_be_relatable_to_create_resources_via_resource()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/posts?viaResource=users&viaResourceId=1&viaRelationship=posts', [
                            'user' => $user3->id,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(422);
    }

    public function test_resource_that_belongs_to_parent_via_has_one_can_be_created()
    {
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/addresses?viaResource=users&viaResourceId=1&viaRelationship=address', [
                            'user' => $user->id,
                            'name' => 'Fake Name',
                        ]);

        $response->assertStatus(201);
    }

    public function test_related_resource_cant_be_full_for_has_one_relationships()
    {
        $user = factory(User::class)->create();
        $user->address()->save($address = factory(Address::class)->make());

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/addresses?viaResource=users&viaResourceId=1&viaRelationship=address', [
                            'user' => $user->id,
                            'name' => 'Fake Name',
                        ]);

        $response->assertStatus(422);
    }

    public function test_related_resource_should_be_able_to_be_updated_even_when_full()
    {
        $user = factory(User::class)->create();
        $user->address()->save($address = factory(Address::class)->make());

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/addresses/'.$address->id.'?viaResource=users&viaResourceId=1&viaRelationship=address', [
                            'user' => $user->id,
                            'name' => 'Fake Name',
                        ]);

        $response->assertStatus(200);
    }

    public function test_can_create_resources_with_null_relation_without_autonull()
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class);

        $response = $this->withExceptionHandling()
            ->postJson('/nova-api/posts', [
                'title' => 'Test Post',
                'user' => '',
            ]);

        $response->assertStatus(201);

        $post = Post::first();

        $this->assertNull($post->user_id);
    }

    public function test_action_event_should_honor_custom_polymorphic_type_for_resource_creation()
    {
        Relation::morphMap(['user' => User::class]);

        $this->withExceptionHandling()
             ->postJson('/nova-api/users', [
                'name' => 'Taylor Otwell',
                'email' => 'taylor@laravel.com',
                'password' => 'secret',
             ]);

        $user = User::first();
        $actionEvent = ActionEvent::first();

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Create', $actionEvent->name);

        $this->assertEquals('user', $actionEvent->actionable_type);
        $this->assertEquals($user->id, $actionEvent->actionable_id);

        $this->assertEquals('user', $actionEvent->target_type);
        $this->assertEquals($user->id, $actionEvent->target_id);

        $this->assertEquals('user', $actionEvent->model_type);
        $this->assertEquals($user->id, $actionEvent->model_id);

        $this->assertTrue($user->is($actionEvent->target));

        Relation::morphMap([], false);
    }
}
