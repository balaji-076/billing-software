<?php

namespace App\Services;

use App\Models\Products;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    private ?Cloudinary $cloudinary = null;

    private function cloudinary(): Cloudinary
    {
        if ($this->cloudinary === null) {
            $this->cloudinary = new Cloudinary(config('cloudinary.cloud_url'));
        }
        return $this->cloudinary;
    }

    private function uploadImage($imageFile): array
    {
        $result = $this->cloudinary()->uploadApi()->upload($imageFile->getRealPath(), [
            'folder' => 'billing_system/products',
        ]);

        return [
            'image_url'            => $result['secure_url'],
            'cloudinary_public_id' => $result['public_id'],
        ];
    }

    private function deleteImage(?string $publicId): void
    {
        if (!$publicId) {
            return;
        }

        try {
            $this->cloudinary()->uploadApi()->destroy($publicId);
        } catch (\Throwable $e) {
            Log::warning('Cloudinary image delete failed', [
                'public_id' => $publicId,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function getPaginatedProducts(Request $request)
    {
        $query = Products::query();

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('product_name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        return $query->latest()->paginate(15);
    }

    public function findProductById(int $id): Products
    {
        return Products::with('store')->findOrFail($id);
    }

    public function createProductRecord(array $data, $imageFile = null): Products
    {
        unset($data['image']);

        // upload outside the DB transaction (network call)
        if ($imageFile) {
            $data = array_merge($data, $this->uploadImage($imageFile));
        }

        try {
            return DB::transaction(fn () => Products::create($data));
        } catch (\Throwable $e) {
            // DB failed after upload -> remove the orphan image
            $this->deleteImage($data['cloudinary_public_id'] ?? null);
            throw $e;
        }
    }

    public function updateProductRecord(Products $product, array $data, $imageFile = null): Products
    {
        unset($data['image']); // the file is never a column value

        $oldPublicId = $product->cloudinary_public_id;

        if ($imageFile) {
            $data = array_merge($data, $this->uploadImage($imageFile));
        }

        try {
            $product = DB::transaction(function () use ($product, $data) {
                $product->update($data);
                return $product->fresh();
            });
        } catch (\Throwable $e) {
            if ($imageFile) {
                $this->deleteImage($data['cloudinary_public_id'] ?? null); // remove the new upload
            }
            throw $e;
        }

        // delete the old image only after the DB update succeeded
        if ($imageFile) {
            $this->deleteImage($oldPublicId);
        }

        return $product;
    }

    public function deleteProductRecord(Products $product): ?bool
    {
        $publicId = $product->cloudinary_public_id;

        $deleted = DB::transaction(fn () => $product->delete());

        $this->deleteImage($publicId);

        return $deleted;
    }
}