<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdController extends Controller
{
    public function index()
    {
        $adBannerSizes = Ad::$size;
        return view('ad.index', compact('adBannerSizes'));
    }

    public function get(Request $request)
    {
        $data = Ad::query();
        $status = Ad::$status;
        $size = Ad::$size;

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('size_name', function ($row) use ($size) {
                return isset($size[$row->size]) ? $size[$row->size] : "";
            })
            ->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->status]) ? $status[$row->status] : "";
            })
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('title', 'LIKE', "%$search%");
                    });
                }
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function addupdate(Request $request)
    {
        if ($request->ajax()) {
            $rules = array(
                'title' => 'required|string',
                'status' => 'required',
                'size' => 'required',
                'url' => 'required|url',
            );

            $messages['title.required'] = trans('validation.custom.title.required');
            $messages['status.required'] = trans('validation.custom.status.required');
            $messages['size.required'] = trans('validation.custom.size.required');
            $messages['url.required'] = trans('validation.custom.url.required');

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Ad_added_successfully');
                if ($request->id) {
                    $model = Ad::where('id', $request->id)->first();
                    if ($model) {
                        $ad = $model;
                        $succssmsg = trans('translation.Ad_updated_successfully');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $ad = new Ad();
                }

                $ad->title = $request->title;
                $ad->status = $request->status;
                $ad->size = $request->size;
                $ad->url = $request->url;

                if ($request->hasFile('image') && $request->image) {
                    if ($request->hidden_image) {
                        Storage::disk("public")->delete('ad_image/' . $request->hidden_image);
                    }

                    $dir = "public/ad_image/";
                    $extension = $request->file("image")->getClientOriginalExtension();
                    $filename = "ad_image" . uniqid() . "_" . time() . "." . $extension;
                    Storage::disk("local")->put($dir . $filename, File::get($request->file("image")));
                    $ad->image = $filename;
                }

                if ($ad->save()) {
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

    public function detail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            $ad = Ad::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $ad];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $ad = Ad::where('id', $request->id);
        if ($ad->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }

    public function preview()
    {
        $loginUser = Auth::user();
        $roles = User::$role;
        if ($loginUser->role_type == 1) {
            return view('ad.preview', compact('loginUser', 'roles'));
        } else {
            return redirect()->route('home');
        }
    }
    public function previewFetch()
    {
        $ads = [
            'size_1' => Ad::where('status', 1)->where('size', 1)->inRandomOrder()->limit(3)->get(),
            'size_2' => Ad::where('status', 1)->where('size', 2)->inRandomOrder()->first(),
            'size_3' => Ad::where('status', 1)->where('size', 3)->inRandomOrder()->first(),
            'size_4' => Ad::where('status', 1)->where('size', 4)->inRandomOrder()->first(),
        ];

        $data = [
            'number-of-dealers' => 100,
            'number-of-brands' => 100,
            'number-of-products' => 100,
        ];

        return response()->json(['status' => true, 'data' => ['data' => $data, 'ads' => $ads]], 200);
    }
}
