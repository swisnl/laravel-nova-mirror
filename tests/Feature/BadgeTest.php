<?php

namespace Laravel\Nova\Tests\Feature;

use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Tests\IntegrationTest;

class BadgeTest extends IntegrationTest
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function test_badge_resolves_correct_value_and_display_class()
    {
        $field = Badge::make('Status');

        $field->resolve((object) ['status' => 'danger']);

        $result = $field->jsonSerialize();

        $this->assertEquals('danger', $result['value']);
        $this->assertEquals('bg-danger-light text-danger-dark', $result['typeClass']);
        $this->assertEquals('Danger', $result['label']);
    }

    public function test_computed_badge_resolves_correct_value_and_display_class()
    {
        $field = Badge::make('Status', function () {
            return 'info';
        });

        $field->resolve((object) []);

        $result = $field->jsonSerialize();

        $this->assertEquals('info', $field->value);
        $this->assertEquals('bg-info-light text-info-dark', $result['typeClass']);
        $this->assertEquals('Info', $result['label']);
    }

    public function test_badge_with_custom_class_map_returns_correct_class()
    {
        $field = Badge::make('Status')->map([
            'draft' => 'info',
            'published' => 'success',
        ]);

        $field->resolve((object) ['status' => 'published']);

        $result = $field->jsonSerialize();

        $this->assertEquals('published', $field->value);
        $this->assertEquals('bg-success-light text-success-dark', $result['typeClass']);
        $this->assertEquals('Published', $result['label']);
    }

    public function test_computed_badge_with_custom_class_map_returns_correct_class()
    {
        $field = Badge::make('Status', function () {
            return 'draft';
        })->map([
            'draft' => 'warning',
            'published' => 'success',
        ]);

        $field->resolve((object) []);

        $result = $field->jsonSerialize();

        $this->assertEquals('draft', $field->value);
        $this->assertEquals('bg-warning-light text-warning-dark', $result['typeClass']);
        $this->assertEquals('Draft', $result['label']);
    }

    public function test_badge_can_be_customized_with_custom_css_classes()
    {
        $field = Badge::make('Status', function () {
            return 'draft';
        })->types([
            'draft' => 'custom class names',
        ]);

        $field->resolve((object) []);

        $result = $field->jsonSerialize();

        $this->assertEquals('draft', $field->value);
        $this->assertEquals('custom class names', $result['typeClass']);
    }

    public function test_badge_can_be_customized_with_custom_css_classes_as_array()
    {
        $field = Badge::make('Status', function () {
            return 'draft';
        })->types([
            'draft' => ['custom', 'class', 'names'],
        ]);

        $field->resolve((object) []);

        $result = $field->jsonSerialize();

        $this->assertEquals('draft', $field->value);
        $this->assertEquals(['custom', 'class', 'names'], $result['typeClass']);
    }

    public function test_badge_can_use_a_custom_label()
    {
        $field = Badge::make('Status', function () {
            return 'danger';
        })->label(function ($value) {
            return 'Custom: '.$value;
        });

        $field->resolve((object) []);

        $result = $field->jsonSerialize();

        $this->assertEquals('danger', $field->value);
        $this->assertEquals('bg-danger-light text-danger-dark', $result['typeClass']);
        $this->assertEquals('Custom: danger', $result['label']);
    }
}
