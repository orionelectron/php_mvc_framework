<?php

namespace orion\core;

class FormField
{
    protected $name;
    protected $type;
    protected $options;

    protected $value;
    protected $labels = [];

    public function __construct($name, $type, $value, $options = [], $labels = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
        $this->value = $value;
        $this->labels = $labels;
    }

    public function render($errors = [])
    {
        $html = '<div class="form-group" style="margin: 10px; display: flex; flex-direction: column;">';
        $html .= '<label style="margin-bottom: 5px; font-weight: bold; font-family: Arial" for="' . $this->name . '">' . ucfirst($this->labels[$this->name] ?? $this->name) . ':</label>';

        switch ($this->type) {
            case 'text':
            case 'email':
            case 'password':
                $html .= '<input style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 1.2rem;" type="' . $this->type . '" id="' . $this->name . '" name="' . $this->name . '" value="'  . htmlspecialchars($this->value) . '" placeholder="' . ucfirst($this->labels[$this->name] ?? $this->name) . '">';
                break;
            case 'checkbox':
                $html .= '<input style="width: auto;" type="checkbox" id="' . $this->name . '" name="' . $this->name . '"' . ($this->value ? ' checked' : '') . '>';
                break;
            case 'radio':
                foreach ($this->options as $option) {
                    $html .= '<label><input type="radio" name="' . $this->name . '" value="' . htmlspecialchars($option['value']) . '"' . ($this->value === $option['value'] ? ' checked' : '') . '> ' . htmlspecialchars($option['label']) . '</label>';
                }
                break;
            case 'select':
                $html .= '<select id="' . $this->name . '" name="' . $this->name . '">';
                foreach ($this->options as $option) {
                    $html .= '<option value="' . htmlspecialchars($option['value']) . '"' . ($this->value === $option['value'] ? ' selected' : '') . '>' . htmlspecialchars($option['label']) . '</option>';
                }
                $html .= '</select>';
                break;
            case 'textarea':
                $html .= '<textarea style="width: auto; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 1.2rem;" id="' . $this->name . '" name="' . $this->name . '" placeholder="' . ucfirst($this->labels[$this->name]) . '">' . htmlspecialchars($this->value) . '</textarea>';
                break;
        }

        if (!empty($errors[$this->name])) {
            $html .= '<div class="error-field" style="color: red;"> (*)  ' . $errors[$this->name][0] . '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}
