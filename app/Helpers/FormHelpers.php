<?php

namespace App\Helpers;

class FormHelpers
{
    public static function validationMessages()
    {
        return [
            'in'      => 'The :attribute must be one of the following types: :values',
            'integer' => 'The :attribute must be an integer',
            'string' => 'The :attribute must be form of text',
            'required' => 'The :attribute field is required',
            'date_format' => 'The :attribute field must be a date_format "Y-m-d H:i:s" string',
            'nullable' => 'The :attribute field is required but is okay to be null',
            'array' => 'The :attribute field must ba an array',
            'exists' => 'The :attribute must exist in the database',
            'unique' => 'The attribute must be unique'
        ];
    }
}
