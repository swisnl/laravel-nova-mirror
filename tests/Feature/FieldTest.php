<?php

namespace Laravel\Nova\Tests\Feature;

use Illuminate\Http\Request;
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

    public function test_fields_can_have_custom_display_callback()
    {
        $field = Text::make('Name')->displayUsing(function ($value) {
            return strtoupper($value);
        });

        $field->resolve((object) ['name' => 'Taylor'], 'name');
        $this->assertEquals('Taylor', $field->value);

        $field->resolveForDisplay((object) ['name' => 'Taylor'], 'name');
        $this->assertEquals('TAYLOR', $field->value);
    }

    public function test_fields_can_have_custom_resolver_callback()
    {
        $field = Text::make('Name')->resolveUsing(function ($value) {
            return strtoupper($value);
        });

        $field->resolve((object) ['name' => 'Taylor'], 'name');

        $this->assertEquals('TAYLOR', $field->value);
    }

    public function test_computed_fields_resolve()
    {
        $field = Text::make('InvokableComputed', function () {
            return 'Computed';
        });

        $field->resolve((object) []);
        $this->assertEquals('Computed', $field->value);

        $field->resolveForDisplay((object) []);
        $this->assertEquals('Computed', $field->value);
    }

    public function test_can_see_when_proxies_to_gate()
    {
        unset($_SERVER['__nova.ability']);

        $field = Text::make('Name')->canSeeWhen('view-profile');
        $callback = $field->seeCallback;

        $request = Request::create('/', 'GET');

        $request->setUserResolver(function () {
            return new class {
                public function can($ability, $arguments = [])
                {
                    $_SERVER['__nova.ability'] = $ability;

                    return true;
                }
            };
        });

        $this->assertTrue($callback($request));
        $this->assertEquals('view-profile', $_SERVER['__nova.ability']);
    }
}
