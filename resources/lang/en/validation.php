<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'The :attribute must be a valid email address.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters. ssss',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',
    'uuid' => 'The :attribute must be a valid UUID.',
    'min' => 'The :attribute must be at least :min characters.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [

        'customer_vat_number' => [
            'required_if' => 'The vat number field is required.',
            'max' => 'Please enter valid vat number',
        ],
        'customer_enquiry_id' => [
            'required' => 'The customer id field is required.',
        ],
        'brand_id' => [
            'required' => 'The brand field is required.',
        ],
        'type_id' => [
            'required' => 'The type field is required.',
        ],
        'category_id' => [
            'required' => 'The category field is required',
        ],
        'appo_date' => [
            'required' => 'The date field is required.',
        ],
        'appo_time' => [
            'required' => 'The time field is required.',
        ],
        'dealer_id' => [
            'required' => 'The dealer field is required.',
        ],
        'sproduct_id' => [
            'required' => 'The product field is required.',
        ],
        'brand_name' => [
            'required' => 'The brand name field is required.',
        ],
        'phone' => [
            'required' => 'The phone field is required.',
            'min' => 'Please enter valid phone number',
        ],
        'birth_date' => [
            'before' => 'Customer must be over 18 years old to become a customer.',
        ],
        'customer_company_name' => [
            'required_if' => 'The company name field is required.',
            'max' => 'Please enter valid customer company name',
        ],
        'shop_start_time' => [
            'required' => 'The shop start time field is required.',
            'date_format' => 'Please enter valid shop start time',
        ],
        'shop_end_time' => [
            'required' => 'The shop end time field is required.',
            'date_format' => 'Please enter valid shop end time',
            'after' => 'The shop end time must be a date after shop start time.',
        ],
        'title' => [
            'required' => 'The title field is required.',
            'max' => 'Please enter  maximum 100 character',

        ],
        'event_date' => [
            'required' => 'The event date field is required.',
        ],
        'event_time' => [
            'required' => 'The event time field is required.',
        ],
        'category' => [
            'required' => 'The category field is required.',
        ],
        'description' => [
            'required' => 'The Description field is required.',
        ],
        'company_name' => [
            'required' => 'The company name field is required.',
            'max' => 'Please enter  maximum 100 character',
        ],
        'street' => [
            'required' => 'The street field is required.',
        ],
        'house_number' => [
            'required' => 'The house number field is required.',
            'max' => 'Please enter valid number',
        ],
        'zipcode' => [
            'required' => 'The zipcode field is required.',
            'numeric' => 'Please enter valid zipcode',
            'digits_between' => 'Please enter valid zipcode',
        ],
        'city' => [
            'required' => 'The city field is required.',
        ],
        'country' => [
            'required' => 'The country field is required.',
        ],
        'vat_number' => [
            'required' => 'The vat number field is required.',
            'max' => 'Please enter valid number',
            'numeric' => 'The vat must be a number.',
            'gt' => 'vat must be greater then 0.'

        ],
        "email" => [
            "required" => "The email field is required.",
            "exists" => "The email  is not  exists.",
            "email" => "The email must be a valid email address.",
            "regex" => "The email must be a valid email address.",
        ],
        "bank_name" => [
            "required" => "The bank name field is required.",
        ],
        "iban" => [
            "required" => "The Iban field is required.",
        ],
        "agbs_terms" => [
            "required" => "Accept the terms and condition.",
        ],
        "bic" => [
            "required" => "The bic field is required.",
        ],
        "first_name" => [
            "required" => "The first name field is required.",
            'max' => 'Please enter  maximum 100 character',
        ],
        "last_name" => [
            "required" => "The last name field is required.",
            'max' => 'Please enter  maximum 100 character',
        ],
        "salutation" => [
            "required" => "The salutation field is required.",
        ],
        "document_file" => [
            "required" => "The Business Registration PDF is required.",
            "mimes" => "Please upload a valid pdf file",
        ],
        "shipping_company" => [
            "required" => "The shipping company field is required.",
        ],
        "tracking_number" => [
            "required" => "The tracking number field is required.",
        ],
        "cancel_proof" => [
            "required" => "The cancel proof field is required.",
        ],
        "password" => [
            "required" => "The password field is required.",
            "min" => "Requires at least 8 characters",
            "confirmed" => "The password confirmation does not match.",
        ],
        "password_confirmation" => [
            "required" => "The confirm password field is required.",
        ],
        "category_name" => [
            "required" => "The category name field is required.",
            'max' => 'Please enter  maximum 100 character',
        ],
        "connection_name" => [
            "required" => "The connection name field is required.",
            'max' => 'Please enter  maximum 100 character',
        ],
        "product_name" => [
            "required" => "The product name field is required.",
        ],
        "product_csv" => [
            "required" => "The field is required.",
        ],
        "retail" => [
            "required" => "The retail field is required.",
            'numeric' => 'Please enter valid number',
        ],
        "url" => [
            "required" => "The url field is required.",
            'url' => 'Please enter valid url',
        ],
        "execution_name" => [
            "required" => "The execution name field is required",
            'max' => 'Please enter  maximum 100 character',
        ],
        "type_name" => [
            "required" => "The type name field is required",
            'max' => 'Please enter  maximum 100 character',
        ],
        "enquiry_description" => [
            "required" => "The purchase note field is required.",
            'max' => 'Please enter  maximum 1000 character',
        ],
        "note" => [
            'max' => 'Please enter  maximum 1000 character',
        ],
        "enquiry_type" => [
            "required" => "The product attribute field is required.",
        ],
        "selected_product_ids" => [
            "required" => "Select any product.",
        ],
        "offer_description" => [
            "required" => "The description field is required",
            'max' => 'Please enter maximum 1000 character',
        ],
        "delivery_time" => [
            "required" => 'The delivery time field is required.',
            "min" => "Days should be greater than 1",
            "numeric" => "Days should be Number",
            'max' => 'Please enter maximum 99 character',
        ],
        "total_vat_amount" => [
            "required" => 'Vat amount field is required.',
            "gt" => "Vat amount should be greater than 0",
            "numeric" => "Vat amount should be Number",
        ],
        "offer_amount" => [
            "required" => "The offer amount field is required.",
            "numeric" => "The offer amount must be a number.",
        ],
        "payment_method" => [
            "required" => "The payment method  field is required.",
        ],
        "dsgvo_terms" => [
            "required" => "Accept the terms and condition.",
        ],
        "sepa_terms" => [
            "required" => "The sepa terms field is required.",
        ],
        "withdrawal_declaration" => [
            "required" => "Accept the withdrawal declaration.",
        ],
        "german_title" => [
            "required" => "The German title field is required.",
            'max' => 'Please enter  maximum 100 character',
        ],
        "german_description" => [
            "required" => "The German description field is required.",
        ],
        "status" => [
            "required" => "The status is required.",
        ],
        "size" => [
            "required" => "The size is required.",
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
