<?php

namespace Laravel\Nova\Tests\Controller;

use Laravel\Nova\Tests\MySqlIntegrationTest;

class MySqlSearchControllerTest extends MySqlIntegrationTest
{
    use SearchControllerTests;

    public function setUp() : void
    {
        if (($_ENV['RUN_MYSQL_TESTS'] ?? false) === false) {
            $this->markTestSkipped('MySQL tests not enabled.');

            return;
        }

        parent::setUp();

        $this->authenticate();
    }
}
