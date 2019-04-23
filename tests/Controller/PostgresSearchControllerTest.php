<?php

namespace Laravel\Nova\Tests\Controller;

use Laravel\Nova\Tests\PostgresIntegrationTest;

class PostgresSearchControllerTest extends PostgresIntegrationTest
{
    use SearchControllerTests;

    public function setUp() : void
    {
        if (($_ENV['RUN_POSTGRES_TESTS'] ?? false) === false) {
            $this->markTestSkipped('Postgres tests not enabled.');

            return;
        }

        parent::setUp();

        $this->authenticate();
    }
}
