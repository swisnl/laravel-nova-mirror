<?php

namespace Laravel\Nova\Tests\Feature;

use Laravel\Nova\Actions\Action;
use Laravel\Nova\Tests\IntegrationTest;

class ActionTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_action_messages_can_be_generated()
    {
        $this->assertEquals(['message' => 'test'], Action::message('test'));
    }

    public function test_action_downloads_can_be_generated()
    {
        $this->assertEquals(['download' => 'test', 'name' => 'name'], Action::download('test', 'name'));
    }
}
