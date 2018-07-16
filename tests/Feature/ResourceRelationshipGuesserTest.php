<?php

namespace Laravel\Nova\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\UserResource;
use Laravel\Nova\Tests\Fixtures\RelationshipGuesserResource;

class ResourceRelationshipGuesserTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_resource_can_be_guessed()
    {
        $fields = (new RelationshipGuesserResource(new Fluent))->fields(Request::create('/'));
        $this->assertEquals(UserResource::class, $fields[1]->resourceClass);
    }
}
