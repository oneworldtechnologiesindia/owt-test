<?php

namespace App\Http\Controllers;

use App\Models\PlanType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PlanTypeController extends Controller
{
    public function index()
    {
        try {
            return view('plan_type.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function get(Request $request)
    {
        try {
            $planTypes = PlanType::query();

            return DataTables::of($planTypes)
                ->editColumn('created_at', function ($planType) {
                    return $planType->created_at->format('d-m-Y');
                })
                ->editColumn('updated_at', function ($planType) {
                    return $planType->updated_at->format('d-m-Y');
                })
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => 'Something went wrong!']);
        }
    }

    public function detail(Request $request)
    {
        try {
            $id = $request->id;
            $planType = PlanType::find($id);
            return response()->json(['status' => true, 'data' => $planType]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => 'Something went wrong!']);
        }
    }

    public function addupdate(Request $request)
    {
        try {
            $id = $request->id;
            $planType = PlanType::find($id);

            if ($planType) {
                $data = [];
                $data['silver_level'] = $request->silver_level;
                $data['gold_level'] = $request->gold_level;
                $data['platinum_level'] = $request->platinum_level;
                $data['diamond_level'] = $request->diamond_level;
                $planType->update($data);
                return response()->json(['status' => true, 'message' => 'Plan type updated successfully!']);
            } else {
                return response()->json(['status' => false, 'error' => 'Plan type not found!']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => 'Something went wrong!']);
        }
    }
}
