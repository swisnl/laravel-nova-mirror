<?php

namespace Laravel\Nova\Tests\Feature;

use stdClass;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Tests\IntegrationTest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Tests\Fixtures\UserResource;

class FieldTest extends IntegrationTest
{
    public function setUp() : void
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

    public function test_computed_fields_resolve_with_resource()
    {
        $field = Text::make('InvokableComputed', function ($resource) {
            return $resource->value;
        });

        $field->resolve((object) ['value' => 'Computed']);
        $this->assertEquals('Computed', $field->value);

        $field->resolveForDisplay((object) ['value' => 'Other value']);
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

    public function test_textarea_fields_dont_show_their_content_by_default()
    {
        $textarea = Textarea::make('Name');
        $trix = Trix::make('Name');
        $markdown = Trix::make('Name');

        $this->assertFalse($textarea->shouldBeExpanded());
        $this->assertFalse($trix->shouldBeExpanded());
        $this->assertFalse($markdown->shouldBeExpanded());
    }

    public function test_textarea_fields_can_be_set_to_always_show_their_content()
    {
        $textarea = Textarea::make('Name')->alwaysShow();
        $trix = Trix::make('Name')->alwaysShow();
        $markdown = Trix::make('Name')->alwaysShow();

        $this->assertTrue($textarea->shouldBeExpanded());
        $this->assertTrue($trix->shouldBeExpanded());
        $this->assertTrue($markdown->shouldBeExpanded());
    }

    public function test_textarea_fields_can_have_custom_should_show_callback()
    {
        $callback = function () {
            return true;
        };

        $textarea = Textarea::make('Name')->shouldShow($callback);
        $trix = Trix::make('Name')->shouldShow($callback);
        $markdown = Trix::make('Name')->shouldShow($callback);

        $this->assertTrue($textarea->shouldBeExpanded());
        $this->assertTrue($trix->shouldBeExpanded());
        $this->assertTrue($markdown->shouldBeExpanded());
    }

    public function test_text_fields_can_be_serialized()
    {
        $field = Text::make('Name');

        $this->assertContains([
            'component' => 'text-field',
            'prefixComponent' => true,
            'indexName' => 'Name',
            'name' => 'Name',
            'attribute' => 'name',
            'value' => null,
            'panel' => null,
            'sortable' => false,
            'textAlign' => 'left',
        ], $field->jsonSerialize());
    }

    public function test_text_fields_can_have_extra_meta_data()
    {
        $field = Text::make('Name')->withMeta(['extraAttributes' => [
            'placeholder' => 'This is a placeholder',
        ]]);

        $this->assertContains([
            'extraAttributes' => ['placeholder' => 'This is a placeholder'],
        ], $field->jsonSerialize());
    }

    public function test_select_fields_options_with_additional_parameters()
    {
        $expected = [
            ['label' => 'A', 'value' => 'a'],
            ['label' => 'B', 'value' => 'b'],
            ['label' => 'C', 'value' => 'c'],
            ['label' => 'D', 'value' => 'd', 'group' => 'E'],
        ];
        $field = Select::make('Name')->options([
            'a' => 'A',
            'b' => ['label' => 'B'],
            ['value' => 'c', 'label' => 'C'],
            ['value' => 'd', 'label' => 'D', 'group' => 'E'],
        ]);

        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($field->jsonSerialize()['options']));
    }

    public function test_field_can_be_set_to_readonly()
    {
        $field = Text::make('Avatar');
        $field->readonly(true);

        $this->assertTrue($field->isReadonly(NovaRequest::create('/', 'get')));
    }

    public function test_field_can_be_set_to_readonly_using_a_callback()
    {
        $field = Text::make('Avatar');
        $field->readonly(function () {
            return true;
        });

        $this->assertTrue($field->isReadonly(NovaRequest::create('/', 'get')));
    }

    public function test_field_can_be_set_to_not_be_readonly_using_a_callback()
    {
        $field = Text::make('Avatar');
        $field->readonly(function () {
            return false;
        });

        $this->assertFalse($field->isReadonly(NovaRequest::create('/', 'get')));
    }

    public function test_collision_of_request_properties()
    {
        $request = new NovaRequest([], [
            'query' => '',
            'resource' => 'resource',
        ]);

        $request->setMethod('POST');
        $request->setRouteResolver(function () use ($request) {
            return tap(new Route('POST', '/{resource}', function () {
            }), function (Route $route) use ($request) {
                $route->bind($request);
                $route->setParameter('resource', UserResource::class);
            });
        });

        $model = new stdClass();

        Text::make('Resource')->fill($request, $model);
        Password::make('Query')->fill($request, $model);

        $this->assertObjectNotHasAttribute('query', $model);
        $this->assertEquals('resource', $model->resource);
    }
}
