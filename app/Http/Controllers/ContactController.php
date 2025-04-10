<?php

namespace App\Http\Controllers;

use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Contact;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function contactsIndex(){
        $countries = User::$countries;
        $contact_types = Contact::$contact_type;
        $salutations = Contact::$salutation;
        return view('contacts.index',compact('countries','contact_types','salutations'));
    }

    public function get(Request $request)
    {
        $data = Contact::query()->where('dealer_id',Auth::user()->id);
        $contact_types = Contact::$contact_type;
        $salutations = Contact::$salutation;

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('contact_type', function ($row) use($contact_types) {
                $result = isset($contact_types[$row->contact_type]) ? $contact_types[$row->contact_type] : null;
                return $result;
            })
            ->editColumn('salutation', function ($row) use($salutations) {
                $result = isset($salutations[$row->salutation]) ? $salutations[$row->salutation] : null;
                return $result;
            })
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('name', 'LIKE', "%$search%");
                    });
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function addupdate(Request $request){
        if ($request->ajax()) {
            $rules = array(
                'contact_type' => 'required',
                'company' => 'required',
                'salutation' => 'required',
                'name' => 'required|string|max:100',
                'surname' => 'required|string|max:100',
                'street' => 'required',
                'street_nr' => 'required',
                'zipcode' => ['required', 'numeric', 'digits_between:4,10'],
                'city' => 'required|string',
                'country' => 'required|string',
                'telephone' => "required|numeric|min:10",
                'note' => 'required',
            );
            if ($request->id) {
                $rules['email'] = 'required|string|email|max:50|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:contacts,email,' . $request->id . ',id,deleted_at,NULL';
            } else {
                $rules['email'] = 'required|string|email|max:50|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:contacts,email,NULL,id,deleted_at,NULL';
            }
            $message['street_nr.required'] = "Nearest Street address is require.";
            $validator = Validator::make($request->all(), $rules,$message);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Contact_added_successfully');
                if ($request->id) {
                    $model = Contact::where('id', $request->id)->first();
                    if ($model) {
                        $contact = $model;
                        $succssmsg = trans('translation.Contact_updated_successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $contact = new Contact;
                    $contact->created_at = Carbon::now();
                }

                $contact->dealer_id = Auth::user()->id;
                $contact->contact_type = $request->contact_type;
                $contact->status = 1;
                $contact->company = $request->company;
                $contact->salutation = $request->salutation;
                $contact->name = $request->name;
                $contact->surname = $request->surname;
                $contact->email = $request->email;
                $contact->street = $request->street;
                $contact->street_nr = $request->street_nr;
                $contact->zipcode = $request->zipcode;
                $contact->city = $request->city;
                $contact->country = $request->country;
                $contact->telephone = $request->telephone;
                $contact->note = $request->note;
                $contact->updated_at = Carbon::now();

                if ($contact->save()) {
                    $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
                } else {
                    $result = ['status' => false, 'message' => 'Error in saving data', 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
        }
        return response()->json($result);
    }

    public function detail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        $contact_types = Contact::$contact_type;
        $salutations = Contact::$salutation;
        if ($request->ajax()) {
            $brand = Contact::find($request->id);
            if(isset($request->view_data) && $request->view_data == true){
                $brand->contact_type = isset($contact_types[$brand->contact_type]) ? $contact_types[$brand->contact_type] : null;
                $brand->salutation = isset($salutations[$brand->salutation]) ? $salutations[$brand->salutation] : null;
                $brand->created_at_view =  ($brand->created_at)? date('d.m.Y H:i', strtotime($brand->created_at)) : "";
            }
            $result = ['status' => true, 'message' => '', 'data' => $brand];
        }
        return response()->json($result);
        exit();
    }
    public function delete(Request $request)
    {
        $brand = Contact::where('id', $request->id);
        if ($brand->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }

    public function uploadContacts(Request $request)
    {
        if ($request->ajax()) {
            $rules = array(
                'contact_csv' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                if ($request->contact_csv) {
                    try {
                        $extension = $request->contact_csv->getClientOriginalExtension();
                        $extension_array = array('csv');
                        if (!in_array($extension, $extension_array)) {
                            $result = ['status' => false, 'message' => trans('translation.Please_upload_CSV_file_only')];
                            return $result;
                        }
                        $dir = "public/contacts-import-csv/";
                        $filename = "contact_csv_".Auth::user()->id . "_" . time() . "." . $extension;
                        Storage::disk("local")->put($dir . $filename,File::get($request->file("contact_csv")));
                        Excel::import(new ContactsImport, $request->contact_csv);
                    } catch (ValidationException $e) {
                        // Catch validation exception if any
                        return response()->json(['status' => false, 'message' => $e->getMessage()], 400);

                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        // Catch Excel import validation exception
                        $failures = $e->failures();
                        \Log::error('Excel import error: ' . $e->getMessage());
                        return response()->json(['status' => false, 'message' => 'Import failed', 'errors' => $failures], 400);

                    } catch (\Exception $e) {
                        // Catch all other exceptions
                        \Log::error('Error: ' . $e->getMessage());
                        return response()->json(['status' => false, 'message' => 'There is template issue in csv file.'], 400);
                    }
                }
                $result = ['status' => true, 'message' => trans('translation.Contacts_Imported_Successfully'), 'error' => []];
            }
            return $result;
        }
    }
}
