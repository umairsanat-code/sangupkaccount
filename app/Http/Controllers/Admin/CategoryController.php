<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\Admin\Categories\StoreRequest;
use App\Http\Requests\Admin\Categories\UpdateRequest;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    use FileUploadTrait;

    public function __construct()
    {
        $this->middleware(['permission:Create App Categories|View Category Detail|Delete Category|
        Add Subcategory|Edit Category|View & Delete Subcategories']);
    }
    /**
     * imageDirectory  path of folder where image will be stored on s3 e.g uploads/categories
     *
     * @var string
     */
    protected $imageDirectory = "uploads/categories";

    /**
     * listing  : list all category using yajra/laravel-datatables-oracle: "~9.0"
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {
        if ($request->ajax()) {
            $this->timeZone = $request->timeZone;
            return \Yajra\DataTables\DataTables::of(Category::get())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->user()->can('Delete Category')) {
                        $btn .= ' <button data-id="' . $row->id  . '" data-name="' . ucfirst($row->name)  . '" class="btn btn-danger btn-sm deleteCategory" title="Delete Category!"><i class="fa fa-trash"></i></button>';
                    }
                    if (auth()->user()->can('Edit Category')) {
                        $btn .= ' <button data-id="' . $row->id  . '" data-name="' . ucfirst($row->name)  . '" data-image="' . $this->getfileUrl($row->imageUrl) . '" class="text-white btn btn-info btn-sm btn-flat categoryEdit" title="Edit Category!"><i class="fa fa-edit"></i></button>';
                        return $btn;
                    }
                })
                ->addColumn('subcategoriesaction', function ($row) {
                    $btn = '';
                    if (auth()->user()->can('View & Delete Subcategories')) {
                        $btn = ' <button data-id="' . $row->id . '" data-name="' . ucfirst($row->name) . '" class="text-white btn btn-info btn-sm btn-flat show" title="View Subcategory!"><i class="fa fa-folder"></i> View </button>';
                    }
                    if (auth()->user()->can('Add Subcategory')) {
                        $btn .= ' <button  data-id="' . $row->id . '" data-name="' . ucfirst($row->name) . '" class="btn btn-success btn-sm btn-flat add" title="Add Subcategory!"><i class="fa fa-plus"></i> Add </button>';
                        return $btn;
                    } else {
                        return ' ';
                    }
                })
                ->addColumn('subcategoriescount', function ($row) {
                    $sucategoriesCount = SubCategory::where('categoryId', $row->id)->count();
                    if ($sucategoriesCount > 0) {
                        return $sucategoriesCount;
                    } else {
                        return 0;
                    }
                })
                ->editColumn('name', function ($row) {
                    $imageUrl = $row->imageUrl ? $this->getfileUrl($row->imageUrl) : asset('img/no_image.png');
                    $name = '<img src="' . $imageUrl . '" alt="Product 1" class="mr-2 img-circle img-size-32">' . $row->name . '';
                    return $name;
                })
                ->rawColumns(['name', 'action', 'subcategoriescount', 'subcategoriesaction'])
                ->make(true);
        }
        return view('admin.categories.listing');
    }

    public function store(StoreRequest $request)
    {

        $imageName = uniqid().'.'.$request->image->getClientOriginalExtension();
        $imageCategory = Image::make($request->image)->resize(524, 524);
        $imageName=rtrim($this->imageDirectory, '/').'/'.$imageName;
        Storage::disk('s3')->put($imageName, $imageCategory->stream());
        Category::create(['name' => ucwords($request->name), 'imageUrl' =>  $imageName]);
        return response()->json(['status' => true, 'data' => ucwords($request->name) . ' category added successfully!'], JsonResponse::HTTP_OK);
    }

    public function edit($id)
    {
        try {
            $cat = Category::find($id);
            return response()->json(['status' => true, 'data' => $cat], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }

    public function update(UpdateRequest $request)
    {

        try {
            $imageName = '';
            $category = Category::find($request->categoryId);
            if ($request->has('image')) {
                $this->deleteFile($category->imageUrl);
                $imageName = uniqid().'.'.$request->image->getClientOriginalExtension();
                $imageCategory = Image::make($request->image)->resize(524, 524);
                $imageName=rtrim($this->imageDirectory, '/').'/'.$imageName;
                Storage::disk('s3')->put($imageName, $imageCategory->stream());
            }
            $category->update(
                [
                    'name' => $request->has('nameEdited') ? ucwords($request->nameEdited) : $category->name,
                    'imageUrl' => $request->has('image') ? $imageName : $category->imageUrl
                ]
            );
            toastr()->success('Category updated  successfully!');
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            toastr()->success($e->getMessage());
            return back();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $category = Category::find($id);
            $products = Product::where('categoryId', $id)->get();
            if ($products->isEmpty()) {
                $this->deleteFile($category->imageUrl);
                $subCategory = SubCategory::where('categoryId', $id)->delete();
                $category->delete();
                DB::commit();
                return response()->json(['status' => true, 'data' => 'Category deleted successfully!'], JsonResponse::HTTP_OK);
            } else {
                return response()->json(['status' => false, 'data' => 'You cant delete a category with products!'], JsonResponse::HTTP_OK);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }
}
