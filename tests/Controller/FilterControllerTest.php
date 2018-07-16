<?php

namespace Laravel\Nova\Tests\Controller;

use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\IdFilter;

class FilterControllerTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_can_retrieve_filters_for_a_resource()
    {
        $response = $this->withExceptionHandling()
                        ->get('/nova-api/users/filters');

        $response->assertStatus(200);
        $this->assertInstanceOf(IdFilter::class, $response->original[0]);
    }

    public function test_unauthorized_filters_are_not_included()
    {
        $_SERVER['nova.idFilter.canSee'] = false;

        $response = $this->withExceptionHandling()
                        ->get('/nova-api/users/filters');

        unset($_SERVER['nova.idFilter.canSee']);

        $response->assertStatus(200);
        $this->assertEmpty($response->original);
    }

    public function test_empty_filter_list_returned_if_no_filters()
    {
        $response = $this->withExceptionHandling()
                        ->get('/nova-api/addresses/filters');

        $response->assertStatus(200);
        $this->assertEmpty($response->original);
    }
}
