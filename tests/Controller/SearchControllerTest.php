<?php

namespace Laravel\Nova\Tests\Controller;

use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Tests\Fixtures\Post;
use Laravel\Nova\Tests\Fixtures\Role;
use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\UserPolicy;

class SearchControllerTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_can_retrieve_search_results_for_all_searchable_resources()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->withExceptionHandling()
                        ->getJson('/nova-api/search?search=1');

        $response->assertStatus(200);

        $original = $response->original;

        $this->assertEquals('posts', $original[1]['resourceName']);
        $this->assertEquals('PostResources', $original[1]['resourceTitle']);
        $this->assertEquals($post->title, $original[1]['label']);
        $this->assertEquals($user->id, $original[1]['resourceId']);
        $this->assertEquals('http://localhost/nova/resources/posts/'.$post->id, $original[1]['url']);
        $this->assertNull($original[1]['avatar']);

        $this->assertEquals('users', $original[2]['resourceName']);
        $this->assertEquals('UserResources', $original[2]['resourceTitle']);
        $this->assertEquals($user->name, $original[2]['label']);
        $this->assertEquals($user->id, $original[2]['resourceId']);
        $this->assertEquals('http://localhost/nova/resources/users/'.$user->id, $original[2]['url']);
        $this->assertNull($original[2]['avatar']);
    }
}
