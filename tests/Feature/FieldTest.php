<?php

namespace Laravel\Nova\Tests\Feature;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Textarea;
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
            "component" => "text-field",
            "prefixComponent" => true,
            "indexName" => "Name",
            "name" => "Name",
            "attribute" => "name",
            "value" => null,
            "panel" => null,
            "sortable" => false,
            "textAlign" => "left",
        ], $field->jsonSerialize());
    }

    public function test_text_fields_can_have_extra_meta_data()
    {
        $field = Text::make('Name')->withMeta(['extraAttributes' => [
            'placeholder' => 'This is a placeholder'
        ]]);

        $this->assertContains([
            'extraAttributes' => ['placeholder' => 'This is a placeholder']
        ], $field->jsonSerialize());
    }
}
