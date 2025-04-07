<?php

namespace Modules\Invoices\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Invoices\Http\Requests\{InvoiceStoreRequest, InvoiceUpdateRequest};
use Modules\Invoices\Services\InvoiceService;
use Modules\Invoices\Transformers\InvoiceResource;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->invoiceService->getPaginatedInvoices(
            $request->integer('per_page', 15),
            $request->boolean('with_trashed'),
            $request->input('status'),
            $request->input('client_id')
        );

        return response()->json([
            'data' => InvoiceResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
            'message' => 'Invoices retrieved successfully'
        ]);
    }

    public function store(InvoiceStoreRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->createInvoice(
            array_merge($request->validated(), ['created_by' => auth()->user()->uuid])
        );

        return response()->json([
            'data' => new InvoiceResource($invoice),
            'message' => 'Invoice created successfully'
        ], Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $invoice = $this->invoiceService->getInvoiceById($id);
        return response()->json([
            'data' => new InvoiceResource($invoice),
            'message' => 'Invoice retrieved successfully'
        ]);
    }

    public function update(InvoiceUpdateRequest $request, string $id): JsonResponse
    {
        $invoice = $this->invoiceService->updateInvoice(
            $id,
            array_merge($request->validated(), ['last_updated_by' => auth()->user()->uuid])
        );

        return response()->json([
            'data' => new InvoiceResource($invoice),
            'message' => 'Invoice updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->invoiceService->deleteInvoice($id);
        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }
}
