<?php

namespace Modules\SiteVisit\Services;

use Modules\SiteVisit\Models\SiteVisit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class SiteVisitService
{
    public function getAllVisits(?string $status = null): Builder
    {
        $query = SiteVisit::with(['assignee', 'creator', 'lead', 'client']);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query;
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
        $data['created_by'] = auth()->user()->uuid; // or auth()->user()->id
        return SiteVisit::create($data);
    }

    public function updateVisit(string $id, array $data): SiteVisit
    {
        $data['last_updated_by'] = auth()->user()->uuid; // or auth()->user()->id
        $visit = $this->getVisitById($id);
        $visit->update($data);
        return $visit->fresh(); // Return refreshed model
    }

    public function updateStatus(string $id, string $status): SiteVisit
    {
       
        $visit = $this->getVisitById($id);
        $visit->update(['status' => $status]);
        return $visit->fresh();
    }

    public function deleteVisit(string $id): bool
    {
        $visit = $this->getVisitById($id);
        return $visit->delete();
    }
}
