<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductHistory;
use App\Models\Type;
use App\Models\Size;
use App\Models\Additonal;
use App\Models\Category;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);}


    public function index($isHistory = false)
    {
        if ($isHistory) {
            $products = ProductHistory::with('user')->latest()->paginate(5);
            return view('Product.history', compact('products'));
        }

        $products = Product::with('user')->where('is_active', 1)->latest()->paginate(5);
        return view('Product.index', compact('products'));
    }

    public function create()
    {
        $types = Type::where('is_active', 1)->get();
        $sizes = Size::where('is_active', 1)->get();
        $additionals = Additonal::where('is_active', true)->get();
        $categories = Category::where('is_active', 1)->get();

        return view('Product.create', compact('types', 'sizes', 'additionals', 'categories'));
    }
   public function search(Request $request)
{
    $searchTerm = $request->input('search');$isHistory=false;
    $products = OffersType::with(['user']) // eager load user + branch
            ->where('name_en', 'like', '%' . $searchTerm . '%')
              ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
             ->orWhere('description_en', 'like', '%' . $searchTerm . '%')->orWhere('description_en', 'like', '%' . $searchTerm . '%')

        ->orderBy('created_at', 'desc')
        ->paginate(5);

        return view('product.index', compact('products'));

}
      public function searchHistory(Request $request)
{
    $searchTerm = $request->input('search');$isHistory=false;
    $products = ProductHistory::with(['user']) // eager load user + branch
            ->where('name_en', 'like', '%' . $searchTerm . '%')
              ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
             ->orWhere('description_en', 'like', '%' . $searchTerm . '%')->orWhere('description_en', 'like', '%' . $searchTerm . '%')

        ->orderBy('created_at', 'desc')
        ->paginate(5);

          return view('product.history',compact('products'));

}
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $product = new Product($validated);
            $product->user_id = auth()->id();
            $product->is_active = $request->has('is_active') ? 1 : 0;
            $product->colors = $validated['colors'] ?? [];
            $product->save();
// B) Only sync when the field was submitted (keeps existing if omitted)
if (array_key_exists('sizes', $validated) && $validated['sizes'] !== null)  {
    $product->sizes()->sync($validated['sizes'] ?? []);
}

if (array_key_exists('additionals', $validated)&& $validated['additionals'] !== null) { // note: correct spelling
    $product->additionals()->sync($validated['additionals'] ?? []);
}

if($request->hasFile('main_image')){
    $mainImageFile = $request->file('main_image');
    $path = $mainImageFile->store('products', 'public');
    $imageUrl = $request->getSchemeAndHttpHost() . '/storage/' . $path;
    $product->main_image = $imageUrl;
        $product->save();

}

        if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $imageUrl = $request->getSchemeAndHttpHost() . '/storage/' . $path;
                    $product->images()->create(['image_path' => $imageUrl]);
                }
            }

            DB::commit();
            return redirect()->route('product.index')->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $product = Product::with(['category', 'sizes', 'additionals', 'images', 'type'])->findOrFail($id);
        $types = Type::where('is_active', 1)->get();
        $sizes = Size::where('is_active', 1)->get();
        $additionals = Additonal::where('is_active', true)->get();
        $categories = Category::where('is_active', 1)->get();

        return view('Product.edit', compact('product', 'types', 'sizes', 'additionals', 'categories'));
    }
    public function show($id)
    {
        $product = Product::with(['category', 'sizes', 'additionals', 'images', 'type','user'])->findOrFail($id);
        return view('Product.show', compact('product'));
    }

    public function update(ProductRequest $request, $id)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $product = Product::with(['sizes', 'additionals', 'images'])->findOrFail($id);

            // Save old product into history
            $this->storeProductInHistory($product, false);

            // Update product
            $product->fill($validated);
            $product->user_id = auth()->id();
            $product->is_active = $request->has('is_active') ? 1 : 0;
           $product->colors = $validated['colors'] ?? [];
            $product->save();
            $product->sizes()->sync($validated['sizes'] ?? []);
            $product->additionals()->sync($validated['additional'] ?? []);

if($request->hasFile('main_image')){
    $mainImageFile = $request->file('main_image');
    $path = $mainImageFile->store('products', 'public');
    $imageUrl = $request->getSchemeAndHttpHost() . '/storage/' . $path;
    $product->main_image = $imageUrl;
        $product->save();

}


            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $imageUrl = $request->getSchemeAndHttpHost() . '/storage/' . $path;
                    $product->images()->update(['image_path' => $imageUrl]);
                }
            }

broadcast(new \App\Events\ProductEventUpdate($product));

            DB::commit();
            return redirect()->route('product.index')->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $product = Product::with(['sizes', 'additionals', 'images'])->findOrFail($id);

            // Save old product into history with inactive flag
            $this->storeProductInHistory($product, false);

            $product->delete();

            DB::commit();
            return redirect()->route('product.index')->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }

    public function reactivate($id)
    {
        try {
            DB::beginTransaction();


            $history = ProductHistory::with([ 'additionals', 'images','sizes'])->findOrFail($id);

            $newProduct = new Product([
                'name_en' => $history->name_en,
                'name_ar' => $history->name_ar,
                'description_en' => $history->description_en,
                'description_ar' => $history->description_ar,
                'price' => $history->price,
                'is_active' => true,
                'user_id' => auth()->id(),
                'category_id' => $history->category_id,
                'type_id' => $history->type_id,
              //  'colors' => json_decode($history->colors, true) ?? []
            ]);
            $newProduct->save();
            if ($history->sizes && $history->sizes->isNotEmpty()) {
                $newProduct->sizes()->sync($history->sizes->pluck('size_id')->toArray());
            }
            $newProduct->additionals()->sync($history->additionals->pluck('additional_id')->toArray());

            foreach ($history->images as $image) {
                $newProduct->images()->create(['image_path' => $image->image_path]);
            }

            DB::commit();
            return redirect()->route('product.index')->with('success', 'Product reactivated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reactivate product: ' . $e->getMessage()]);
        }
    }

    /**
     * Store the current product into ProductHistory (shared method).
     * @param Product $product
     * @param bool $isActive
     */
    private function storeProductInHistory(Product $product, bool $isActive = false)
    {
        $history = new ProductHistory([
            'name_en' => $product->name_en,
            'name_ar' => $product->name_ar,
            'description_en' => $product->description_en,
            'description_ar' => $product->description_ar,
            'price' => $product->price,
            'is_active' => $isActive,
            'user_id' => auth()->id(),
            'category_id' => $product->category_id,
            'type_id' => $product->type_id,
            'original_product_id' => $product->id,
            'colors' => ($product->colors ?? [])
        ]);
        $history->save();

        // Copy relations
        if ($product->sizes) {
         $history->sizes()->sync($product->sizes->pluck('id')->toArray());
        }

        if ($product->additionals) {
$historyAdditionals = [];

foreach ($product->additionals as $additional) {
    $historyAdditionals[] = [
        'additional_id' => $additional->id,
        'product_id' => $history->original_product_id,
        // add other fields if your history table needs them
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

// insert them all at once if using Eloquent:
$history->additionals()->createMany($historyAdditionals);
        }
if ($product->images && $product->images->count()) {
    foreach ($product->images as $image) {
        $history->images()->create([
            'image_path' => $image->image_path,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
    }
    }

