<?php

namespace App\Imports;

use App\Models\Contact;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ContactsImport implements ToModel,WithHeadingRow,WithStartRow,WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $contact_types = Contact::$contact_type;
        $salutations = Contact::$salutation;
        $countries = User::$countries;

        if ($row['contact_type'] != "" &&$row['salutation'] != "" && $row['company_name'] != "" && $row['email'] != "" && $row['name'] != "" && $row['surname'] != "" && $row['city'] != "" && $row['country'] != "")
        {
            if(in_array($row['contact_type'], $contact_types) && in_array($row['salutation'], $salutations) && in_array($row['country'], $countries)){
                $contact = array_search ($row['contact_type'], $contact_types);
                $salutation = array_search ($row['salutation'], $salutations);
                $country = $row['country'];
                $dealer_id = Auth::user()->id;
                Contact::create([
                    'dealer_id' => $dealer_id,
                    'contact_type' => $contact,
                    'company' => $row['company_name'],
                    'salutation' => $salutation,
                    'name' => $row['name'],
                    'surname' => $row['surname'],
                    'email' => $row['email'],
                    'street' => $row['street'],
                    'street_nr' => $row['nearest_street'],
                    'zipcode' => $row['zip_code'],
                    'city' => $row['city'],
                    'country' => $country,
                    'telephone' => $row['telephone'],
                    'note' => $row['note'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            // '*.0' => 'required',
            // '*.1' => 'required',
            // '*.2' => 'required',
            // '*.3' => 'required',
            // '*.4' => 'required',
            // '*.5' => 'required',
            // '*.10' => 'required',
            // '*.11' => 'required',
        ];
    }
    public function startRow(): int
    {
        return 2; // Start from the third row (ignoring the first two rows)
    }
}
