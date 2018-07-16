<?php

namespace Laravel\Nova\Tests\Feature;

use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Tests\Fixtures\Tag;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\TagPolicy;
use Laravel\Nova\Tests\Fixtures\TagResource;
use Laravel\Nova\Tests\Fixtures\ForbiddenUserResource;

class ResourceAuthorizationTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_resources_are_not_available_if_not_authorized()
    {
        $available = Nova::availableResources(Request::create('/'));

        $this->assertFalse(in_array(ForbiddenUserResource::class, $available));
    }

    public function test_resource_is_automatically_authorizable_if_it_has_policy()
    {
        $this->assertFalse(TagResource::authorizable());

        Gate::policy(Tag::class, TagPolicy::class);

        $this->assertTrue(TagResource::authorizable());
    }
}
