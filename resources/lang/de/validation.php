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
        'string' => 'The :attribute may not be greater than :max characters.',
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
    'required' => 'Das :attribute-Feld ist erforderlich.',
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
    'unique' => 'Das :attribute wurde bereits vergeben.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',
    'uuid' => 'The :attribute must be a valid UUID.',

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
            'required_if' => 'Das Feld mit der Umsatzsteuer-Identifikationsnummer ist erforderlich.',
            'max' => 'Bitte geben Sie eine gültige Umsatzsteuer-Identifikationsnummer ein',
        ],
        'customer_enquiry_id' => [
            'required' => 'Das Feld Kunden-ID ist erforderlich.',
        ],
        'brand_id' => [
            'required' => 'Das Markenfeld ist erforderlich.',
        ],
        'type_id' => [
            'required' => 'Das Typfeld ist erforderlich.',
        ],
        'category_id' => [
            'required' => 'Das Kategoriefeld ist erforderlich',
        ],
        'appo_date' => [
            'required' => 'Das Datumsfeld ist erforderlich.',
        ],
        'appo_time' => [
            'required' => 'Das Zeitfeld ist erforderlich.',
        ],
        'dealer_id' => [
            'required' => 'Das Händlerfeld ist erforderlich.',
        ],
        'sproduct_id' => [
            'required' => 'Das Produktfeld ist erforderlich.',
        ],
        'brand_name' => [
            'required' => 'Das Feld Markenname ist erforderlich.',
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein.',
        ],
        'phone' => [
            'required' => 'Das Telefonfeld ist erforderlich.',
            'min' => 'Bitte geben Sie eine gültige Telefonnummer ein',
        ],
        'birth_date' => [
            'before' => 'Um Kunde zu werden, muss der Kunde über 18 Jahre alt sein.',
        ],
        'customer_company_name' => [
            'required_if' => 'Das Feld Firmenname ist erforderlich.',
            'max' => 'Bitte geben Sie einen gültigen Firmennamen des Kunden ein',
        ],
        'shop_start_time' => [
            'required' => 'Das Feld Shop-Startzeit ist erforderlich.',
            'date_format' => 'Bitte geben Sie eine gültige Shop-Startzeit ein',
        ],
        'shop_end_time' => [
            'required' => 'Das Feld Shop-Endzeit ist erforderlich.',
            'date_format' => 'Bitte geben Sie eine gültige Shop-Endzeit ein',
            'after' => 'Die Shop-Endzeit muss ein Datum nach der Shop-Startzeit sein.',
        ],
        'title' => [
            'required' => 'Das Titelfeld ist erforderlich.',
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein',

        ],
        'event_date' => [
            'required' => 'Das Feld Ereignisdatum ist erforderlich.',
        ],
        'event_time' => [
            'required' => 'Das Feld Ereigniszeit ist erforderlich.',
        ],
        'category' => [
            'required' => 'Das Kategoriefeld ist erforderlich.',
        ],
        'description' => [
            'required' => 'Das Feld Beschreibung ist erforderlich.',
        ],
        'company_name' => [
            'required' => 'Das Feld Firmenname ist erforderlich.',
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein',
        ],
        'street' => [
            'required' => 'Das Straßenfeld ist erforderlich.',
        ],
        'house_number' => [
            'required' => 'Das Feld Hausnummer ist erforderlich.',
            'max' => 'Bitte geben Sie eine gültige Nummer ein',
        ],
        'zipcode' => [
            'required' => 'Das Feld Postleitzahl ist erforderlich.',
            'numeric' => 'Bitte geben Sie eine gültige Postleitzahl ein',
            'digits_between' => 'Bitte geben Sie eine gültige Postleitzahl ein',
        ],
        'city' => [
            'required' => 'Das Stadtfeld ist erforderlich.',
        ],
        'country' => [
            'required' => 'Das Feld Land ist erforderlich.',
        ],
        'vat_number' => [
            'required' => 'Das Feld mit der Umsatzsteuer-Identifikationsnummer ist erforderlich.',
            'max' => 'Bitte geben Sie eine gültige Nummer ein',
            'numeric' => 'Die Mehrwertsteuer muss eine Zahl sein.',
            'gt' => 'Mehrwertsteuer muss größer als 0 sein.'
        ],
        "email" => [
            "required" => "Das E-Mail-Feld ist erforderlich.",
            "exists" => "The email  is not  exists.",
            "email" => "Bei der E-Mail muss es sich um eine gültige E-Mail-Adresse handeln.",
            "regex" => "Bei der E-Mail muss es sich um eine gültige E-Mail-Adresse handeln.",
            'max' => 'Bei der E-Mail muss es sich um eine gültige E-Mail-Adresse handeln.',
        ],
        "bank_name" => [
            "required" => "Das Feld Bankname ist erforderlich.",
        ],
        "iban" => [
            "required" => "Das IBAN-Feld ist erforderlich.",
        ],
        "agbs_terms" => [
            "required" => "Accept the terms and condition.",
        ],
        "bic" => [
            "required" => "Das BIC-Feld ist erforderlich.",
        ],
        "first_name" => [
            "required" => "Vorname ist erforderlich.",
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein',
        ],
        "last_name" => [
            "required" => "Das Feld Nachname ist erforderlich.",
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein',
        ],
        "salutation" => [
            "required" => "Das Anredefeld ist erforderlich.",
        ],
        "document_file" => [
            "required" => "The Business Registration PDF is required.",
            "mimes" => "Please upload a valid pdf file",
        ],
        "shipping_company" => [
            "required" => "Das Feld Versandunternehmen ist erforderlich.",
        ],
        "tracking_number" => [
            "required" => "Das Feld für die Sendungsverfolgungsnummer ist erforderlich.",
        ],
        "cancel_proof" => [
            "required" => "Das Feld Stornierungsnachweis ist erforderlich.",
        ],
        "password" => [
            "required" => "Das Passwortfeld ist erforderlich.",
            "min" => "Erfordert mindestens 8 Zeichen",
            "confirmed" => "Die Passwortbestätigung stimmt nicht überein.",
        ],
        "password_confirmation" => [
            "required" => "Das Feld Passwort bestätigen ist erforderlich.",
        ],
        "category_name" => [
            "required" => "Das Feld Kategoriename ist erforderlich.",
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein',
        ],
        "connection_name" => [
            "required" => "Das Feld Verbindungsname ist erforderlich.",
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein',
        ],
        "product_name" => [
            "required" => "Das Feld Produktname ist erforderlich.",
        ],
        "product_csv" => [
            "required" => "The field is required.",
        ],
        "retail" => [
            "required" => "Der Einzelhandelsbereich ist erforderlich.",
            'numeric' => 'Bitte geben Sie eine gültige Nummer ein',
        ],
        "url" => [
            "required" => "Das URL-Feld ist erforderlich.",
            'url' => 'Bitte geben Sie eine gültige URL ein',
        ],
        "execution_name" => [
            "required" => "Das Feld Ausführungsname ist erforderlich",
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein',
        ],
        "type_name" => [
            "required" => "Das Feld Typname ist erforderlich.",
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein',
        ],
        "enquiry_description" => [
            "required" => "The purchase note field is required.",
            'max' => 'Please enter  maximum 1000 character',
        ],
        "note" => [
            'max' => 'Bitte geben Sie maximal 1000 Zeichen ein',
        ],
        "enquiry_type" => [
            "required" => "The product attribute field is required.",
        ],
        "selected_product_ids" => [
            "required" => "Wählen Sie ein beliebiges Produkt aus.",
        ],
        "offer_description" => [
            "required" => "Das Beschreibungsfeld ist erforderlich",
            'max' => 'Bitte geben Sie maximal 1000 Zeichen ein',
        ],
        "delivery_time" => [
            "required" => 'Das Feld Lieferzeit ist erforderlich.',
            "min" => "Tage sollten größer als 1 sein",
            "numeric" => "Tage sollten Zahl sein",
            'max' => 'Bitte geben Sie maximal 99 Zeichen ein',
        ],
        "total_vat_amount" => [
            "required" => 'Das Feld Mehrwertsteuerbetrag ist erforderlich.',
            "gt" => "Der Mehrwertsteuerbetrag sollte größer als 0 sein",
            "numeric" => "Der Mehrwertsteuerbetrag sollte Zahl sein",
        ],
        "offer_amount" => [
            "required" => "Das Feld Angebotsbetrag ist erforderlich.",
            "numeric" => "Der Angebotsbetrag muss eine Zahl sein.",
        ],
        "payment_method" => [
            "required" => "Das Feld Zahlungsmethode ist erforderlich.",
        ],
        "dsgvo_terms" => [
            "required" => "Akzeptieren Sie die Allgemeinen Geschäftsbedingungen",
        ],
        "sepa_terms" => [
            "required" => "The sepa terms field is required.",
        ],
        "withdrawal_declaration" => [
            "required" => "Akzeptieren Sie die Widerrufserklärung.",
        ],
        "german_title" => [
            "required" => "Das deutsche Titelfeld ist erforderlich.",
            'max' => 'Bitte geben Sie maximal 100 Zeichen ein',
        ],
        "german_description" => [
            "required" => "Das deutsche Beschreibungsfeld ist erforderlich.",
        ],
        "status" => [
            "required" => "Der Status ist erforderlich.",
        ],
        "size" => [
            "required" => "Die Größe ist erforderlich.",
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
