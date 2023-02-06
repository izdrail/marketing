<?php

namespace Cornatul\Marketing\Base\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Cornatul\Marketing\Base\View\Components\CheckboxField;
use Cornatul\Marketing\Base\View\Components\FieldWrapper;
use Cornatul\Marketing\Base\View\Components\FileField;
use Cornatul\Marketing\Base\View\Components\Label;
use Cornatul\Marketing\Base\View\Components\SelectField;
use Cornatul\Marketing\Base\View\Components\SubmitButton;
use Cornatul\Marketing\Base\View\Components\TextareaField;
use Cornatul\Marketing\Base\View\Components\TextField;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component(TextField::class, 'sendportal.text-field');
        Blade::component(TextareaField::class, 'sendportal.textarea-field');
        Blade::component(FileField::class, 'sendportal.file-field');
        Blade::component(SelectField::class, 'sendportal.select-field');
        Blade::component(CheckboxField::class, 'sendportal.checkbox-field');
        Blade::component(Label::class, 'sendportal.label');
        Blade::component(SubmitButton::class, 'sendportal.submit-button');
        Blade::component(FieldWrapper::class, 'sendportal.field-wrapper');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
