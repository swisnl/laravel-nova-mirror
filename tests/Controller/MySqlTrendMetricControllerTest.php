<?php

namespace Laravel\Nova\Tests\Controller;

use Cake\Chronos\Chronos;
use Laravel\Nova\Tests\Fixtures\Post;
use Laravel\Nova\Tests\MySqlIntegrationTest;

class MySqlTrendMetricControllerTest extends MySqlIntegrationTest
{
    use TrendDateTests;

    public function setUp()
    {
        if (($_ENV['RUN_MYSQL_TESTS'] ?? false) === false) {
            $this->markTestSkipped('MySQL tests not enabled.');

            return;
        }

        parent::setUp();

        $this->authenticate();
    }
}
