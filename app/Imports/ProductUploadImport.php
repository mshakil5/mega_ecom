<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Type;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class ProductUploadImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        if (empty($data['name']) || empty($data['seasonsystem_will_make_its_code']) || empty($data['category'])) {
            return null;
        }

        DB::beginTransaction();

        try {

            logger()->info('Importing product:', $data);
            $slug = Str::slug($data['name']);
            $count = Product::where('slug', 'like', "$slug%")->count();
            $uniqueSlug = $count ? "{$slug}-" . ($count + 1) : $slug;

            $season = strtoupper(Str::slug($data['seasonsystem_will_make_its_code']));
            $latest = Product::where('product_code', 'like', "STL-{$season}-" . date('Y') . '-%')
                ->orderByDesc('product_code')
                ->first();
            $next = $latest ? intval(substr($latest->product_code, -5)) + 1 : 1;
            $productCode = "STL-{$season}-" . date('Y') . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);

            $category = Category::firstOrCreate(
                ['name' => $data['category']],
                ['slug' => Str::slug($data['category'])]
            );

            $subcategory = SubCategory::firstOrCreate(
                ['name' => $data['sub_category'] ?? ''],
                ['slug' => Str::slug($data['sub_category'] ?? '')]
            );

            $product = Product::create([
                'name' => $data['name'],
                'slug' => $uniqueSlug,
                'product_code' => $productCode,
                'category_id' => $category->id,
                'sub_category_id' => $subcategory->id,
                'short_description' => $data['short_description'] ?? '',
                'long_description' => $data['long_description'] ?? '',
                'created_by' => auth()->id(),
                'feature_image' => $data['image'] ?? '',
            ]);

            $typeNames = array_filter(array_map('trim', explode(',', $data['types_comma_separated'] ?? '')));
            $typeIds = [];

            foreach ($typeNames as $typeName) {
                if (!$typeName) continue;

                $type = Type::firstOrCreate(
                    ['name' => $typeName],
                    ['slug' => Str::slug($typeName)]
                );

                $typeIds[] = $type->id;
            }

            if (!empty($typeIds)) {
                $product->types()->sync($typeIds);
            }
            logger()->info('Product created with ID: ' . $product->id);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Product import error: ' . $e->getMessage());
        }
    }
}