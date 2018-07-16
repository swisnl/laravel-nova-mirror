<?php

namespace Laravel\Nova\Tests\Feature;

use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Tests\Fixtures\TotalUsers;

class MetricTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_metric_can_be_calculated()
    {
        factory(User::class, 2)->create();

        $this->assertEquals(2, (new TotalUsers)->calculate(NovaRequest::create('/'))->value);
    }
}
