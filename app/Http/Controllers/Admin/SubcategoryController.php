<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Subcategories\StoreRequest;

class SubcategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:Create App Categories|View Category Detail|Delete Category|
        Add Subcategory|Edit Category|View & Delete Subcategories']);
    }

    public function get()
    {
        return response()->json(['response' => ['status' => true, 'data' => SubCategory::all()]], JsonResponse::HTTP_OK);
    }

    public function categories($categoryId)
    {
        return response()->json(['status' => true, 'data' => SubCategory::where('categoryId', $categoryId)->get()], JsonResponse::HTTP_OK);
    }

    public function store(StoreRequest $request)
    {
        try {
            $aleadyExistCategory = SubCategory::where('categoryId',$request->categoryId)->where('name',$request->name)->first();
            $requestStatus = false;
            if($aleadyExistCategory)
            {                  
                toastr()->error(ucwords($request->name) . " subcategory aleady exist!");
                return back();
            }

            DB::beginTransaction();
            $created=SubCategory::create([
                'name'=>ucwords($request->name),
                'categoryId'=>$request->categoryId
            ]);
            DB::commit();
            if(isset($created->id)){
                toastr()->success('Subcategory created successfully!');
            }
            return back();

        } catch (Exception $e) {
            DB::rollBack();
            toastr()->error($e->getMessage());
            return back();
        }
    }
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $subCategory=SubCategory::find($id);
            $products=Product::where('subCategoryId',$id)->get();
            if($products->isEmpty()){
                $subCategory->delete();
                DB::commit();
                return response()->json(['status' => true, 'data' => 'Subcategory deleted successfully!'], JsonResponse::HTTP_OK);
            }else{
                return response()->json(['status' =>false, 'data' => 'You cant delete a subcategory with products!'], JsonResponse::HTTP_OK);
            }
            } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' =>false, 'data' =>$e->getMessage()], JsonResponse::HTTP_OK);
        }
    }
}
