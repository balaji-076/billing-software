<?php

namespace App\Services;

use App\Models\Invoices;
use App\Models\Products;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Process line items, verify database prices, and store invoice with JSON snapshot.
     */
    public function processAndStoreInvoice(array $validatedData): Invoices
    {
        return DB::transaction(function () use ($validatedData) {
            $storeId = $validatedData['store_id'];
            $subtotal = 0;
            $calculatedLineItems = [];

            foreach ($validatedData['items'] as $item) {
                $product = Products::where('id', $item['product_id'])
                    ->where('store_id', $storeId)
                    ->firstOrFail();

                // Authoritative database price verification & snapshot creation
                $unitPrice = $product->discount_price ?? $product->original_price;
                $lineTotal = $unitPrice * $item['quantity'];
                $subtotal += $lineTotal;

                $calculatedLineItems[] = [
                    'product_id' => $product->id,
                    'product_name_snapshot' => $product->product_name,
                    'product_code_snapshot' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_original_price' => $product->original_price,
                    'unit_discount_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $overallDiscount = $validatedData['overall_discount_amount'] ?? 0;
            $grandTotal = max(0, $subtotal - $overallDiscount);
            $uniqueInvoiceNumber = $this->generateInvoiceNumber();

            // Create Invoice and store items as JSON
            $invoice = Invoices::create([
                'invoice_number' => $uniqueInvoiceNumber,
                'store_id' => $storeId,
                'customer_name_snapshot' => $validatedData['customer_name'],
                'customer_mobile_snapshot' => $validatedData['customer_mobile'],
                'subtotal' => $subtotal,
                'overall_discount_amount' => $overallDiscount,
                'grand_total' => $grandTotal,
                'payment_method' => $validatedData['payment_method'],
                'items' => $calculatedLineItems, 
                'invoice_status' => 'COMPLETED',
            ]);

            return $invoice;
        });
    }

    /**
     * Retrieve paginated invoices filtered by store and search keyword.
     */
    public function paginateStoreInvoices($request)
    {
        $query = Invoices::query(); 

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function($q) use ($keyword) {
                $q->where('invoice_number', 'like', "%{$keyword}%")
                  ->orWhere('customer_name_snapshot', 'like', "%{$keyword}%")
                  ->orWhere('customer_mobile_snapshot', 'like', "%{$keyword}%");
            });
        }

        return $query->latest()->paginate(15);
    }

    private function generateInvoiceNumber(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $prefix = 'INV-' . now()->format('ymd') . '-';

        do {
            $code = '';
            for ($i = 0; $i < 4; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $number = $prefix . $code;
        } while (Invoices::where('invoice_number', $number)->exists());

        return $number;
    }
}