<?php namespace Anomaly\Streams\Addon\FieldType\Repeating;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Assignment\AssignmentModel;


/**
 * Class RepeatingFieldType
 */
class RepeatingFieldType extends FieldType
{
    public $columnType = 'text';

    protected $rules = [];

    protected $view = 'field_type.repeating::element';

    public function element()
    {
        $locale = $this->getLocale();

        $hidden       = ($this->hidden);
        $required     = $this->getRequired();
        $translatable = $this->getTranslatable();
        $class        = $this->getSlug() . '-field-type';

        $language     = trans("language.{$locale}");
        $label        = trans($this->label, [], null, $locale);
        $instructions = trans($this->instructions, [], null, $locale);

        $fieldAssignment = $this->assignment->field;
        $fieldName = $this->field;

        // GET FIELD INFO SETTINGS

        // @TODO figure out why mutator isn't working
        $settingsJson = $fieldAssignment->settings;
        $settings = $fieldAssignment->getSettingsAttribute($settingsJson);
        $existingFieldGroups = $this->onGet($this->value) ?: [];

        // ASSIGN FIELD TYPE OBJECT TO EACH SETTINGS FIELD

        foreach($settings->field_groups as $group) {

            foreach($group->elements as $element) {

                $element->field = $this->getElementField($element, $group->slug)->element();
            }
        }

        $inputGroups = $settings->field_groups;

        // CREATE ARRAY OF EXISTING FIELDS STORED IN DB
        $formFieldGroups = [];

        $i = 0;

        foreach($existingFieldGroups as $existingFieldGroupSortContainer) {

            foreach($existingFieldGroupSortContainer as $existingFieldGroupSlug => $existingFieldGroupItems) {

                $formFieldGroups[$i] = $this->getGroupFromSlug($existingFieldGroupSlug, $settings);

                foreach($existingFieldGroupItems as $fieldEntrySlug => $fieldEntryValue) {

                    $input = $this->getElementField($this->getElementFromSlug($fieldEntrySlug, $settings), $existingFieldGroupSlug, $i);
                    $input->value = $fieldEntryValue;

                    $formFieldGroups[$i]->fields[$fieldEntrySlug] = $input->element();
                }

                $i++;
            }
        }

        $repeatingGroupId = uniqid();

        $data = compact(
            'label',
            'language',
            'instructions',
            'inputGroups',
            'locale',
            'hidden',
            'class',
            'translatable',
            'required',
            'repeatingGroupId',
            'formFieldGroups',
            'fieldName'
        );

        return view($this->view, $data);
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value ?: \Input::get($this->field);

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    private function getLocale()
    {
        if (!$this->locale) {

            $this->locale = setting('module.settings::default_locale', config('app.locale', 'en'));
        }

        return $this->locale;
    }

    public function getColumnName()
    {
        return $this->field;
    }

    public function getColumnType()
    {
        return $this->columnType;
    }

    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    public function decorate()
    {
        if ($presenter = app('streams.transformer')->toPresenter($this)) {

            return new $presenter($this);
        }

        return new FieldTypePresenter($this);
    }

    protected function getGroupFromSlug($fieldGroupSlug, $settings)
    {
        foreach($settings as $fieldGroups) {
            foreach($fieldGroups as $fieldGroup) {
                if($fieldGroup->slug == $fieldGroupSlug) {

                    $fieldGroupClone = clone($fieldGroup);

                    unset($fieldGroupClone->elements);

                    return $fieldGroupClone;
                }
            }
        }
    }

    protected function getElementFromSlug($fieldEntrySlug, $settings)
    {
        foreach($settings as $field_groups) {
            foreach($field_groups as $field_group) {
                foreach($field_group->elements as $element) {
                    if($element->slug == $fieldEntrySlug) {
                        return clone($element);
                    }
                }
            }
        }
    }

    protected function getElementField(\stdClass $element, $group, $subGroup = 0) {

        switch($element->type) {

            case 'text':

                $textField = app()->make('Anomaly\Streams\Addon\FieldType\Text\TextFieldType');
                $textField->label = $element->label;
                $textField->field = $this->field . '[' . $subGroup . '][' . $group . '][' . $element->slug . ']';

                return $textField;

            case 'url':

                $urlField = app()->make('Anomaly\Streams\Addon\FieldType\Url\UrlFieldType');
                $urlField->label = $element->label;
                $urlField->field = $this->field . '[' . $subGroup . '][' . $group . '][' . $element->slug . ']';

                return $urlField;

            case 'email':

                $checkboxesField = app()->make('Anomaly\Streams\Addon\FieldType\Email\EmailFieldType');
                $checkboxesField->label = $element->label;
                $checkboxesField->field = $this->field . '[' . $subGroup . '][' . $group . '][' . $element->slug . '][]';

                return $checkboxesField;

            case 'wysiwyg':

                $checkboxesField = app()->make('Anomaly\Streams\Addon\FieldType\Wysiwyg\WysiwygFieldType');
                $checkboxesField->label = $element->label;
                $checkboxesField->field = $this->field . '[' . $subGroup . '][' . $group . '][' . $element->slug . '][]';

                return $checkboxesField;

            case 'textarea':

                $checkboxesField = app()->make('Anomaly\Streams\Addon\FieldType\Textarea\TextareaFieldType');
                $checkboxesField->label = $element->label;
                $checkboxesField->field = $this->field . '[' . $subGroup . '][' . $group . '][' . $element->slug . '][]';

                return $checkboxesField;

            case 'integer':

                $checkboxesField = app()->make('Anomaly\Streams\Addon\FieldType\Integer\IntegerFieldType');
                $checkboxesField->label = $element->label;
                $checkboxesField->field = $this->field . '[' . $subGroup . '][' . $group . '][' . $element->slug . '][]';

                return $checkboxesField;
        }
    }

    public function getFieldName()
    {
        return "{$this->getPrefix()}{$this->field}{$this->getSuffix()}";
    }

    protected function onSet($value)
    {
        return base64_encode(json_encode(\Input::get($this->field)));
    }

    protected function onAfterSet($entry)
    {
        //
    }

    protected function onGet($value)
    {
        return ! is_string($value) ? $value :  json_decode(base64_decode($this->value));
    }

    protected function onAssignmentCreated(AssignmentModel $assignment)
    {
        // Run after an assignment is created.
        // @TODO create database tables
    }

    protected function onAssignmentDeleted(AssignmentModel $assignment)
    {
        // Run after an assignment is deleted.
        // @TODO delete database tables?
    }
}
