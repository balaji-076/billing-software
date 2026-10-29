<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Fetch store-wise paginated invoices (Retrieve).
     */
    public function paginateStoreInvoices(Request $request): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->paginateStoreInvoices($request);
            return $this->successResponse('Bills retrieved successfully', $invoices);
        } catch (\Exception $exception) {
            return $this->errorResponse('Failed to retrieve bills: ' . $exception->getMessage(), 500);
        }
    }

    /**
     * Handle incoming invoice creation and processing request (Store).
     */
    public function processAndStoreInvoice(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'customer_name' => 'required|string',
            'customer_mobile' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'overall_discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        try {
            $invoice = $this->invoiceService->processAndStoreInvoice($validatedData);

            return $this->successResponse('Invoice generated successfully', [
                'invoice_number' => $invoice->invoice_number,
                'grand_total' => $invoice->grand_total,
            ], 201);
        } catch (\Exception $exception) {
            return $this->errorResponse('Transaction failed: ' . $exception->getMessage(), 500);
        }
    }
}