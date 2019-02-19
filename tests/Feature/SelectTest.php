<?php

namespace Laravel\Nova\Tests\Feature;

use Laravel\Nova\Fields\Select;
use Laravel\Nova\Tests\IntegrationTest;

class SelectTest extends IntegrationTest
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function test_fields_can_have_custom_display_callback()
    {
        $field = Select::make('Sizes')->options([
            'L' => 'Large',
            'S' => 'Small',
        ])->displayUsingLabels();

        $field->resolve((object) ['size' => 'L'], 'size');
        $this->assertEquals('L', $field->value);

        $field->resolveForDisplay((object) ['size' => 'L'], 'size');
        $this->assertEquals('Large', $field->value);
    }
}
