<?php

namespace orion\core;

use orion\core\FormField;

class Form extends DBModel
{
    protected $fields = [];
    protected $path = '/';

    public function __construct($data = [], $path='/')
    {
        $this->path = $path;
        foreach ($data as $name => $value) {
            $type = 'text';
            $options = [];

            if (preg_match('/gender/i', $name)) {
                $type = 'radio';
                $options = [
                    ['label' => 'Male', 'value' => 'male'],
                    ['label' => 'Female', 'value' => 'female'],
                    ['label' => 'Other', 'value' => 'other']
                ];
            } else if (preg_match('/message|msg/i', $name)) {
                $type = 'textarea';
            } else if (preg_match('/email/i', $name)) {
                $type = 'email';
            } else if (preg_match('/password/i', $name)) {
                $type = 'password';
            }

            $this->addField(new FormField($name, $type, $value, $options, $this->labels()));
        }
    }

    public function addField($field)
    {
        $this->fields[] = $field;
    }

    public function render()
    {
        $output = '';
        foreach ($this->fields as $field) {
            $output .= $field->render($this->errors);
        }
        $output .= '<button type="submit" style="background-color: #007bff; color: #fff; padding: 10px; border: none; border-radius: 5px; width: 100%;">Submit</button>';
        $output = '<form method="post" style="width: 400px; margin: 0 auto;" action="' . $this->path .    '">' . $output . '</form>';
        return  $output;
    }

    public function __toString()
    {
        return $this->render();
    }
}
