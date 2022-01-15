<?php

return [
    'after' => 'The :attribute must be a date after :date.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'name' => 'The :attribute provided is not in accepted format',
    'numeric' => 'The :attribute must be a number.',
    'phone' => 'The :attribute provided is not in acceped format',
    'required' => 'The :attribute field is required',
    'string' => 'The :attribute must be a string.',
    'unique' => 'The :attribute has already been taken.',
];
