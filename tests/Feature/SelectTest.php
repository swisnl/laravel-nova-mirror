<?php

namespace Laravel\Nova\Tests\Feature;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\UserResource;

class FieldTest extends IntegrationTest
{
    public function setUp()
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
