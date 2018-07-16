<?php

namespace Laravel\Nova\Tests\Controller;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Tests\Fixtures\Post;
use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\UserPolicy;

class ResourceUpdateTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_can_update_resources()
    {
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/users/'.$user->id, [
                            'name' => 'Taylor Otwell',
                            'email' => 'taylor@laravel.com',
                            'password' => 'secret',
                        ]);

        $response->assertStatus(200);

        $user = $user->fresh();
        $this->assertEquals('Taylor Otwell', $user->name);
        $this->assertEquals('taylor@laravel.com', $user->email);

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Update', ActionEvent::first()->name);
        $this->assertEquals($user->id, ActionEvent::first()->target->id);
        $this->assertTrue($user->is(ActionEvent::first()->target));
    }

    public function test_cant_update_resource_fields_that_arent_authorized()
    {
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/users/'.$user->id, [
                            'name' => 'Taylor Otwell',
                            'email' => 'taylor@laravel.com',
                            'password' => 'secret',
                            'restricted' => 'No',
                        ]);

        $response->assertStatus(200);

        $user = $user->fresh();
        $this->assertEquals('Taylor Otwell', $user->name);
        $this->assertEquals('taylor@laravel.com', $user->email);
        $this->assertEquals('Yes', $user->restricted);
    }

    public function test_cant_update_resources_that_have_been_edited_since_retrieval()
    {
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/users/'.$user->id, [
                            'name' => 'Taylor Otwell',
                            'email' => 'taylor@laravel.com',
                            'password' => 'secret',
                            '_retrieved_at' => now()->subHours(1)->getTimestamp(),
                        ]);

        $response->assertStatus(409);
    }

    public function test_must_be_authorized_to_update_resource()
    {
        $_SERVER['nova.user.authorizable'] = true;
        $_SERVER['nova.user.updatable'] = false;

        Gate::policy(User::class, UserPolicy::class);

        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/users/'.$user->id, [
                            'name' => 'Taylor Otwell',
                            'email' => 'taylor@laravel.com',
                            'password' => 'secret',
                        ]);

        unset($_SERVER['nova.user.authorizable']);
        unset($_SERVER['nova.user.updatable']);

        $response->assertStatus(403);
    }

    public function test_must_be_authorized_to_relate_related_resource_to_update_a_resource_that_it_belongs_to()
    {
        $post = factory(Post::class)->create();

        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/posts/'.$post->id, [
                            'user' => $user3->id,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(422);
    }

    public function test_parent_resource_policy_may_prevent_adding_related_resources()
    {
        $post = factory(Post::class)->create();
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/posts/'.$post->id, [
                            'user' => $user->id,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(200);

        $_SERVER['nova.user.authorizable'] = true;
        $_SERVER['nova.user.addPost'] = false;

        Gate::policy(User::class, UserPolicy::class);

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/posts/'.$post->id, [
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

    public function test_can_update_soft_deleted_resources()
    {
        $user = factory(User::class)->create();
        $user->delete();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/users/'.$user->id, [
                            'name' => 'Taylor Otwell',
                            'email' => 'taylor@laravel.com',
                            'password' => 'secret',
                        ]);

        $response->assertStatus(200);

        $user = $user->fresh();
        $this->assertEquals('Taylor Otwell', $user->name);
        $this->assertEquals('taylor@laravel.com', $user->email);

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Update', ActionEvent::first()->name);
        $this->assertEquals($user->id, ActionEvent::first()->target->id);
        $this->assertTrue($user->is(ActionEvent::first()->target));
    }

    public function test_user_can_maintain_same_email_without_unique_errors()
    {
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/users/'.$user->id, [
                            'name' => $user->name,
                            'email' => $user->email,
                            'password' => $user->password,
                        ]);

        $response->assertStatus(200);
    }

    public function test_validation_rules_are_applied()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/users/'.$user->id, [
                            'name' => $user->name,
                            'email' => $user2->email,
                            'password' => $user->password,
                        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email',
        ]);
    }

    public function test_resource_with_parent_can_be_updated()
    {
        $post = factory(Post::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/posts/'.$post->id, [
                            'user' => $post->user->id,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(200);
    }

    public function test_parent_resource_must_exist()
    {
        $post = factory(Post::class)->create();

        $response = $this->withExceptionHandling()
                        ->putJson('/nova-api/posts/'.$post->id, [
                            'user' => 100,
                            'title' => 'Fake Title',
                        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user']);
    }
}
