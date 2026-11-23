<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CategorySubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Final
        // https://eloumo-development.s3.us-east-1.amazonaws.com/uploads/categories/category1.jpg

        $category1 = Category::create(["name"=>"Home Furniture","imageUrl"=>"uploads/categories/category1.jpg"]);
                SubCategory::create(["name"=>"Sofa Set","categoryId"=>$category1->id]);
                SubCategory::create(["name"=>"Bed Set","categoryId"=>$category1->id]);

        $this->uploadCategoryImages('category1.jpg');

        $category2 = Category::create(["name"=>"Cars","imageUrl"=>"uploads/categories/category2.jpg"]);
                SubCategory::create(["name"=>"SUVs","categoryId"=>$category2->id]);
                SubCategory::create(["name"=>"Sedan","categoryId"=>$category2->id]);
                SubCategory::create(["name"=>"Mini Pickups","categoryId"=>$category2->id]);
                SubCategory::create(["name"=>"Compact Sedans","categoryId"=>$category2->id]);

        $this->uploadCategoryImages('category2.jpg');

        $category3 = Category::create(["name"=>"Home Appliances","imageUrl"=>"uploads/categories/category3.jpg"]);
                SubCategory::create(["name"=>"Misc.","categoryId"=>$category3->id]);

        $this->uploadCategoryImages('category3.jpg');

        $category4 = Category::create(["name"=>"Mobile Phones","imageUrl"=>"uploads/categories/category4.jpg"]);
                SubCategory::create(["name"=>"Android","categoryId"=>$category4->id]);
                SubCategory::create(["name"=>"Apple","categoryId"=>$category4->id]);

        $this->uploadCategoryImages('category4.jpg');
    }

    function uploadCategoryImages($img){
        $imageStorePath=rtrim("uploads/categories", '/').'/'.$img;
        $imageGetPath='categoriesImages/'.$img;        
        $firstFile = Image::make(public_path($imageGetPath))->resize(300, 300);
        Storage::disk('s3')->put($imageStorePath, $firstFile->stream());
    }
}
