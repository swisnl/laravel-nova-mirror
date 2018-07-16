<?php

namespace Laravel\Nova\Tests\Controller;

use Laravel\Nova\Tests\PostgresIntegrationTest;

/**
 * @group postgres
 */
class PostgresTrendMetricControllerTest extends PostgresIntegrationTest
{
    use TrendDateTests;

    public function setUp()
    {
        if (($_ENV['RUN_POSTGRES_TESTS'] ?? false) === false) {
            $this->markTestSkipped('Postgres tests not enabled.');
        }

        parent::setUp();

        $this->authenticate();
    }
}
