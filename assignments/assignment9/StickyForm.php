<?php
// Include the Validation class which provides the base validation functionality
require_once 'classes/Validation.php';

/**
 * StickyForm Class
 * 
 * This class extends the Validation class to provide form persistence and rendering capabilities.
 * It maintains form values after submission and handles validation of different form field types.
 * The class also provides methods to render various form elements with Bootstrap styling.
 */
class StickyForm extends Validation {

    /**
     * Validates form data against the provided configuration
     * 
     * @param array $data The submitted form data ($_POST)
     * @param array $formConfig The form configuration array defining field types and validation rules
     * @return array Updated form configuration with sticky values and error messages
     */
    public function validateForm($data, $formConfig) {
        foreach ($formConfig as $key => &$element) {
            // Store submitted value to maintain form state
            $element['value'] = $data[$key] ?? '';

            // Get custom error message if specified, otherwise use default
            $customErrorMsg = $element['errorMsg'] ?? null;

            // Handle text and textarea fields with regex validation
            if (isset($element['type']) && in_array($element['type'], ['text', 'textarea']) && isset($element['regex'])) {
                $isValid = $this->checkFormat($element['value'], $element['regex'], $customErrorMsg);
                if (!$isValid) {
                    $element['error'] = $this->getErrors()[$element['regex']];
                }
            }

            // Handle select dropdown validation
            elseif (isset($element['type']) && $element['type'] === 'select') {
                $element['selected'] = $data[$key] ?? '';
                if (isset($element['required']) && $element['required'] && ($element['selected'] === '0' || empty($element['selected']))) {
                    $element['error'] = $customErrorMsg ?? 'This field is required.';
                    $formConfig['masterStatus']['error'] = true;
                }
            }

            // Handle checkbox validation (both single and groups)
            elseif (isset($element['type']) && $element['type'] === 'checkbox') {
                if (isset($element['options'])) {
                    // Handle checkbox groups
                    $anyChecked = false;
                    foreach ($element['options'] as &$option) {
                        $option['checked'] = in_array($option['value'], $data[$key] ?? []);
                        if ($option['checked']) {
                            $anyChecked = true;
                        }
                    }
                    if (isset($element['required']) && $element['required'] && !$anyChecked) {
                        $element['error'] = $customErrorMsg ?? 'This field is required.';
                        $formConfig['masterStatus']['error'] = true;
                    }
                } else {
                    // Handle single checkbox
                    $element['checked'] = isset($data[$key]);
                    if (isset($element['required']) && $element['required'] && !$element['checked']) {
                        $element['error'] = $customErrorMsg ?? 'This field is required.';
                        $formConfig['masterStatus']['error'] = true;
                    }
                }
            }

            // Handle radio button validation
            elseif (isset($element['type']) && $element['type'] === 'radio') {
                $isChecked = false;
                foreach ($element['options'] as &$option) {
                    $option['checked'] = ($option['value'] === ($data[$key] ?? ''));
                    if ($option['checked']) {
                        $isChecked = true;
                    }
                }
                if (isset($element['required']) && $element['required'] && !$isChecked) {
                    $element['error'] = $customErrorMsg ?? 'This field is required.';
                    $formConfig['masterStatus']['error'] = true;
                }
            }
        }

        return $formConfig;
    }

    /**
     * Generates HTML for select dropdown options
     * 
     * @param array $options Array of options with value => label pairs
     * @param string $selectedValue Currently selected value
     * @return string HTML string of option elements
     */
    public function createOptions($options, $selectedValue) {
        $html = '';
        foreach ($options as $value => $label) {
            $selected = ($value == $selectedValue) ? 'selected' : '';
            $html .= "<option value=\"$value\" $selected>$label</option>";
        }
        return $html;
    }

    /**
     * Renders error message if present
     * 
     * @param array $element Form element configuration
     * @return string HTML string of error message or empty string
     */
    private function renderError($element) {
        return !empty($element['error']) ? "<span class=\"text-danger\">{$element['error']}</span><br>" : '';
    }

    /**
     * Renders a text input field with label and error message
     * 
     * @param array $element Form element configuration
     * @param string $class Additional CSS classes
     * @return string HTML string of the input field
     */
    public function renderInput($element, $class = '') {
        $errorOutput = $this->renderError($element);
        return <<<HTML
<div class="$class">
    <label for="{$element['id']}">{$element['label']}</label>
    <input type="text" class="form-control" id="{$element['id']}" name="{$element['name']}">
    $errorOutput
</div>
HTML;
    }

    /**
     * Renders a password input field with label and error message
     * 
     * @param array $element Form element configuration
     * @param string $class Additional CSS classes
     * @return string HTML string of the password field
     */
    public function renderPassword($element, $class = '') {
        $errorOutput = $this->renderError($element);
        return <<<HTML
<div class="$class">
    <label for="{$element['id']}">{$element['label']}</label>
    <input type="password" class="form-control" id="{$element['id']}" name="{$element['name']}">
    $errorOutput
</div>
HTML;
    }

    /**
     * Renders a textarea field with label and error message
     * 
     * @param array $element Form element configuration
     * @param string $class Additional CSS classes
     * @return string HTML string of the textarea
     */
    public function renderTextarea($element, $class = '') {
        $errorOutput = $this->renderError($element);
        return <<<HTML
<div class="$class">
    <label for="{$element['id']}">{$element['label']}</label>
    <textarea class="form-control" id="{$element['id']}" name="{$element['name']}">{$element['value']}</textarea>
    $errorOutput
</div>
HTML;
    }

    /**
     * Renders a group of radio buttons with label and error message
     * 
     * @param array $element Form element configuration
     * @param string $class Additional CSS classes
     * @param string $layout 'horizontal' or 'vertical' arrangement
     * @return string HTML string of the radio button group
     */
    public function renderRadio($element, $class = '', $layout = 'vertical') {
        $errorOutput = $this->renderError($element);
        $optionsHtml = '';
        $layoutClass = $layout === 'horizontal' ? 'form-check-inline' : '';
        foreach ($element['options'] as $option) {
            $checked = $option['checked'] ? 'checked' : '';
            $optionsHtml .= <<<HTML
<div class="form-check $layoutClass">
    <input class="form-check-input" type="radio" id="{$element['id']}_{$option['value']}" name="{$element['name']}" value="{$option['value']}" $checked>
    <label class="form-check-label" for="{$element['id']}_{$option['value']}">{$option['label']}</label>
</div>
HTML;
        }
        return <<<HTML
<div class="$class">
    <label>{$element['label']}</label><br>
    $optionsHtml
    $errorOutput
</div>
HTML;
    }

    /**
     * Renders a single checkbox with label and error message
     * 
     * @param array $element Form element configuration
     * @param string $class Additional CSS classes
     * @param string $layout 'horizontal' or 'vertical' arrangement
     * @return string HTML string of the checkbox
     */
    public function renderCheckbox($element, $class = '', $layout = 'vertical') {
        $checked = $element['checked'] ? 'checked' : '';
        $errorOutput = $this->renderError($element);
        $layoutClass = $layout === 'horizontal' ? 'form-check-inline' : '';
        return <<<HTML
<div class="$class">
    <div class="form-check $layoutClass">
        <input class="form-check-input" type="checkbox" id="{$element['id']}" name="{$element['name']}" $checked>
        <label class="form-check-label" for="{$element['id']}">{$element['label']}</label>
    </div>
    $errorOutput
</div>
HTML;
    }

    /**
     * Renders a group of checkboxes with label and error message
     * 
     * @param array $element Form element configuration
     * @param string $class Additional CSS classes
     * @param string $layout 'horizontal' or 'vertical' arrangement
     * @return string HTML string of the checkbox group
     */
    public function renderCheckboxGroup($element, $class = '', $layout = 'vertical') {
        $errorOutput = $this->renderError($element);
        $optionsHtml = '';
        $layoutClass = $layout === 'horizontal' ? 'form-check-inline' : '';
        foreach ($element['options'] as $index => $option) {
            $checked = $option['checked'] ? 'checked' : '';
            $optionsHtml .= <<<HTML
<div class="form-check $layoutClass">
    <input class="form-check-input" type="checkbox" id="{$element['id']}_{$index}" name="{$element['name']}[]" value="{$option['value']}" $checked>
    <label class="form-check-label" for="{$element['id']}_{$index}">{$option['label']}</label>
</div>
HTML;
        }
        return <<<HTML
<div class="$class">
    <label>{$element['label']}</label><br>
    $optionsHtml
    $errorOutput
</div>
HTML;
    }

    /**
     * Renders a select dropdown with label and error message
     * 
     * @param array $element Form element configuration
     * @param string $class Additional CSS classes
     * @return string HTML string of the select dropdown
     */
    public function renderSelect($element, $class = '') {
        $errorOutput = $this->renderError($element);
        $optionsHtml = $this->createOptions($element['options'], $element['selected']);
        return <<<HTML
<div class="$class">
    <label for="{$element['id']}">{$element['label']}</label>
    <select class="form-control" id="{$element['id']}" name="{$element['name']}">
        $optionsHtml
    </select>
    $errorOutput
</div>
HTML;
    }
}
?>
