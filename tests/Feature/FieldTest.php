<?php

namespace Laravel\Nova\Tests\Feature;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Tests\Fixtures\UserResource;

class FieldTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_component_can_be_customized()
    {
        Text::useComponent('something');
        $this->assertEquals('something', (new Text('Foo', 'foo'))->component());

        $this->assertEquals('belongs-to-field', (new BelongsTo('User', 'user', UserResource::class))->component());
    }

    public function test_fields_can_have_custom_resolver_callback()
    {
        $field = Text::make('Name')->resolveUsing(function ($value) {
            return strtoupper($value);
        });

        $field->resolve((object) ['name' => 'Taylor'], 'name');

        $this->assertEquals('TAYLOR', $field->value);
    }
}
