<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->except('signupGet');
    }

    public function index()
    {
        $status = User::$status;
        return view('documents.index', compact('status'));
    }

    public function addUpdate(Request $request)
    {
        $documents = Document::$types;
        if ($request->ajax() && isset($documents) && isset($request->type) && !empty($request->type) && in_array($request->type, $documents)) {
            $rules = array(
                'title' => 'required|string|max:100',
                'german_title' => 'required|string|max:100',
                'description' => 'required|string',
                'german_description' => 'required|string'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Document added successfully');
                $id = array_search($request->type, $documents);
                $model = Document::find($id);
                if (isset($model->id)) {
                    $document = $model;
                    $succssmsg = trans('translation.Document updated successfully');
                } else {
                    $document = new Document;
                    $document->created_at = Carbon::now();
                }
                $document->title = $request->title;
                $document->description = $request->description;
                $document->german_title = $request->german_title;
                $document->german_description = $request->german_description;

                $document->updated_at = Carbon::now();
                if ($document->save()) {
                    $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Error in saving data'), 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
        }
        return response()->json($result);
    }

    public function get()
    {
        $documents = [];
        $document_collect = collect(Document::all());
        $documents_types = Document::$types;
        if (isset($document_collect) && !empty($document_collect)) {
            foreach ($documents_types as $type_key => $type_value) {
                $data = $document_collect->where('id', $type_key)->first();
                if (isset($data) && !empty($data)) {
                    $documents[$type_value] = $document_collect->where('id', $type_key)->first()->only(['title', 'description','german_title','german_description']);
                }
            }

            if (isset($documents) && !empty($documents)) {
                $result = ['status' => true, 'message' => trans('translation.Data found'), 'data' => $documents];
            } else {
                $result = ['status' => true, 'message' => 'Invalid request', 'data' => []];
            }
        } else {
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
        }
        return response()->json($result);
    }

    public function signupGet(Request $request)
    {
        $document_types = Document::$types;
        if ($request->ajax() && isset($document_types) && isset($request->type) && !empty($request->type) && in_array($request->type, $document_types)) {
            $id = array_search($request->type, $document_types);
            $document = Document::find($id);

            if (isset($document) && !empty($document)) {
                $result = ['status' => true, 'message' => trans('translation.Data found'), 'data' => $document];
            } else {
                $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
            }
        } else {
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
        }
        return response()->json($result);
    }
}
