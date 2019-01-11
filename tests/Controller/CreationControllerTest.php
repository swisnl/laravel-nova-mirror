<?php

namespace Laravel\Nova\Tests\Controller;

use Laravel\Nova\Tests\Fixtures\Post;
use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;

class CreationControllerTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_fields_count()
    {
        $response = $this->withExceptionHandling()
            ->getJson('/nova-api/posts/creation-fields');

        $response->assertJsonCount(2);

        $response = $this->withExceptionHandling()
            ->getJson('/nova-api/comments/creation-fields');

        $response->assertJsonCount(3);
    }

    public function test_related_fields_count()
    {
        $user = factory(User::class)->create();

        $response = $this->withExceptionHandling()
            ->getJson("/nova-api/posts/creation-fields?viaResource=users&viaResourceId={$user->id}&viaRelationship=user");

        $response->assertJsonCount(2);
    }

    public function test_morph_related_fields_count()
    {
        $post = factory(Post::class)->create();

        $response = $this->withExceptionHandling()
            ->getJson("/nova-api/comments/creation-fields?viaResource=posts&viaResourceId={$post->id}&viaRelationship=comments");

        $response->assertJsonCount(3);
    }
}
