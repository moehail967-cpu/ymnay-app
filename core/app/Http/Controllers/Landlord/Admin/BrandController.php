<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Helpers\ResponseMessage;
use App\Helpers\SanitizeInput;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:brand-list|brand-create|brand-edit|brand-delete',['only' => ['index']]);
        $this->middleware('permission:brand-create',['only' => ['store']]);
        $this->middleware('permission:brand-edit',['only' => ['update','clone']]);
        $this->middleware('permission:brand-delete',['only' => ['delete','bulk_action']]);
    }

    public function index()
    {
        $all_brands = Brand::latest()->get();
        return view('landlord.admin.brand.brand',compact('all_brands'));
    }

    public function store(Request $request){
        $this->validate($request,[
            'url' => 'required|string',
            'image' => 'required|string',
            'status' => 'nullable|string',
        ]);

        $data = [
            'url' => SanitizeInput::esc_html($request->url),
            'image' => $request->image,
            'status' => $request->status,
        ];
        Brand::create($data);
        return response()->success(ResponseMessage::SettingsSaved());
    }

    public function update(Request $request){
        $this->validate($request,[
            'url' => 'required|string',
            'image' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        Brand::findOrFail($request->id)->update([
            'url' => SanitizeInput::esc_html($request->url),
            'image' => $request->image,
            'status' => $request->status,
        ]);

        return response()->success(ResponseMessage::SettingsSaved());
    }

    public function delete(Request $request,$id){
        Brand::findOrFail($id)->delete();
        return response()->danger(ResponseMessage::delete());
    }
    public function bulk_action(Request $request)
    {
        try {
            $ids = $request->ids ?? [];

            $brandsInUse = Product::whereIn('brand_id', $ids)->pluck('brand_id')->unique();

            if ($brandsInUse->isNotEmpty()) {
                return response()->json([
                    "success" => false,
                    "message" => "Some brands cannot be deleted because they are linked to products."
                ], 409);
            }
            // Proceed to delete safely
            Brand::whereIn('id', $ids)->delete();

            return response()->json([
                "success" => true,
                "message" => "Brands deleted successfully."
            ]);
        } catch (\Exception $e) {
            // Catch any unexpected database error and return clean message
            return response()->json([
                "success" => false,
                "message" => "An error occurred: " . $e->getMessage(),
            ], 500);
        }
    }



}
