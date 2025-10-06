<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Create categories for each distinct legacy products.category
        $categories = DB::table('products')
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $name) {
            $slug = Str::slug((string)$name);
            $categoryId = strtoupper('CAT-' . substr(hash('crc32b', $slug), 0, 8));

            $exists = DB::table('categories')->where('slug', $slug)->exists();
            if (!$exists) {
                DB::table('categories')->insert([
                    'category_id' => $categoryId,
                    'name' => (string)$name,
                    'slug' => $slug,
                    'status' => 'Active',
                    'parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $categoryId = DB::table('categories')->where('slug', $slug)->value('category_id');
            }

            // Update products to set category_id
            DB::table('products')->where('category', $name)->update([
                'category_id' => $categoryId,
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // On rollback, clear category_id set by this migration for categories we created from legacy names
        $slugs = DB::table('products')->select('category')->whereNotNull('category')->distinct()->pluck('category')->map(fn($n) => Str::slug((string)$n));
        $catIds = DB::table('categories')->whereIn('slug', $slugs)->pluck('category_id');
        DB::table('products')->whereIn('category_id', $catIds)->update(['category_id' => null]);
        // Optionally delete categories created here (commented out to avoid deleting manually added ones)
        // DB::table('categories')->whereIn('category_id', $catIds)->delete();
    }
};


