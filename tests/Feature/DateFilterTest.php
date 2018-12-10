<?php

namespace Laravel\Nova\Tests\Feature;

use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\CreateDateFilter;

class DateFilterTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_first_day_of_week_can_be_changed()
    {
        $filter = (new CreateDateFilter)->firstDayOfWeek(4);

        $this->assertArraySubset([
            'firstDayOfWeek' => 4,
        ], $filter->jsonSerialize());
    }
}
