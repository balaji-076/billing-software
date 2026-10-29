<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function paginateStoreProducts(Request $request): JsonResponse
    {
        $products = $this->productService->getPaginatedProducts($request);
        return $this->successResponse('Products retrieved successfully', $products);
    }

    public function fetchProductDetails(int $id): JsonResponse
    {
        try {
            $product = $this->productService->findProductById($id);
            return $this->successResponse('Product details retrieved successfully', $product);
        } catch (\Exception $e) {
            return $this->errorResponse('Product not found', 404);
        }
    }

    public function persistProductRecord(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'product_name' => 'required|string|max:255',
            'sku' => [
                'required', 'string', 'max:100',
                Rule::unique('products')->where(fn ($query) => $query->where('store_id', $request->store_id)),
            ],
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'original_price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0|lte:original_price',
            'unit' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'informational_qty' => 'nullable|numeric|min:0',
        ]);

        try {
            $imageFile = $request->file('image');
            $createdProduct = $this->productService->createProductRecord($validatedData, $imageFile);

            return $this->successResponse('Product created successfully', $createdProduct, 201);
        } catch (\Exception $exception) {
            return $this->errorResponse('Failed to persist product: ' . $exception->getMessage(), 500);
        }
    }


    public function updateProductRecord(Request $request, int $id): JsonResponse
    {
        $product = Products::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        $validatedData = $request->validate([
            'store_id'          => 'sometimes|exists:stores,id',
            'product_name'      => 'sometimes|string|max:255',
            'sku'               => [
                'sometimes', 'string', 'max:100',
                Rule::unique('products')->where(function ($query) use ($request, $product) {
                    return $query->where('store_id', $request->input('store_id', $product->store_id));
                })->ignore($product->id),
            ],
            'category'          => 'sometimes|string|max:100',
            'description'       => 'nullable|string',
            'original_price'    => 'sometimes|numeric|min:0',
            'discount_price'    => 'sometimes|numeric|min:0',
            'unit'              => 'sometimes|string|max:50',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'informational_qty' => 'nullable|numeric|min:0',
            'status'            => 'sometimes|string|in:ACTIVE,INACTIVE',
        ]);

        try {
            $imageFile = $request->hasFile('image') ? $request->file('image') : null;
            unset($validatedData['image']); // the file is passed separately, never as an array value

            $updatedProduct = $this->productService->updateProductRecord($product, $validatedData, $imageFile);

            return $this->successResponse('Product updated successfully', $updatedProduct);
        } catch (\Throwable $e) {
            Log::error('Product update failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return $this->errorResponse(
                'Failed to update product: ' . $e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine(),
                500
            );
        }
    }
    public function removeProductRecord(int $id): JsonResponse
    {
        $product = Products::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        try {
            $this->productService->deleteProductRecord($product);
            return $this->successResponse('Product deleted successfully');
        } catch (\Exception $exception) {
            return $this->errorResponse('Failed to delete product: ' . $exception->getMessage(), 500);
        }
    }
}