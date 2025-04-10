<?php

namespace Modules\SiteVisit\Services;

use Modules\SiteVisit\Models\SiteVisit;
use Illuminate\Database\Eloquent\Collection;

class SiteVisitService
{
    public function getAllVisits(): Collection
    {
        return SiteVisit::with(['assignee', 'creator', 'lead'])
            ->get();
    }

    public function getVisitById(string $id): SiteVisit
    {
        $visit = SiteVisit::with(['assignee', 'creator', 'lead'])
            ->where('id', $id)
            ->first();
            
        if (!$visit) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("No query results for model [Modules\SiteVisit\Models\SiteVisit] with ID {$id}");
        }
        
        return $visit;
    }

    public function createVisit(array $data): SiteVisit
    {
        $data['created_by'] = auth()->id(); // or auth()->user()->id
        return SiteVisit::create($data);
    }

    public function updateVisit(string $id, array $data): SiteVisit
    {
        $visit = $this->getVisitById($id);
        $visit->update($data);
        return $visit->fresh(); // Return refreshed model
    }

    public function deleteVisit(string $id): bool
    {
        $visit = $this->getVisitById($id);
        return $visit->delete();
    }
}
