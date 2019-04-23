<?php

namespace Laravel\Nova\Tests\Controller;

use Laravel\Nova\Tests\PostgresIntegrationTest;

class PostgresSearchControllerTest extends PostgresIntegrationTest
{
    use SearchControllerTests;

    public function setUp() : void
    {
        parent::setUp();

        $this->authenticate();
    }
}
