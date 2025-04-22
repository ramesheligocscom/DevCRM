<?php

namespace Modules\Quotations\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Quotations\Http\Requests\{QuotationStoreRequest, QuotationUpdateRequest};
use Modules\Quotations\Services\QuotationService;
use Modules\Quotations\Transformers\QuotationResource;
use Symfony\Component\HttpFoundation\Response;

class QuotationController extends Controller
{
    protected $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->quotationService->getPaginatedQuotations(
            $request->integer('per_page', 15),
            $request->boolean('with_trashed'),
            $request->input('status'),
            $request->input('client_id'),
            $request->input('lead_id'),
            $request->input('contract_id'),
            $request->input('created_by'),
            $request->input('last_updated_by'),
            $request->input('search')
        );

        return response()->json([
            'data' => QuotationResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
            'message' => 'Quotations retrieved successfully'
        ]);
    }

    public function store(QuotationStoreRequest $request): JsonResponse
    {
        $quotation = $this->quotationService->createQuotation(
            array_merge($request->validated(), ['created_by' => auth()->user()->uuid])
        );

        return response()->json([
            'data' => new QuotationResource($quotation),
            'message' => 'Quotation created successfully'
        ], Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $quotation = $this->quotationService->getQuotationById($id);
        return response()->json([
            'data' => new QuotationResource($quotation),
            'message' => 'Quotation retrieved successfully'
        ]);
    }

    public function update(QuotationUpdateRequest $request, string $id): JsonResponse
    {
        $quotation = $this->quotationService->updateQuotation(
            $id,
            array_merge($request->validated(), ['last_updated_by' => auth()->user()->uuid])
        );

        return response()->json([
            'data' => new QuotationResource($quotation),
            'message' => 'Quotation updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->quotationService->deleteQuotation($id);
        return response()->json([
            'message' => 'Quotation deleted successfully'
        ]);
    }
}
