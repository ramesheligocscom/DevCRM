<?php

namespace Modules\SiteVisit\Services;

use Modules\SiteVisit\Models\SiteVisit;
use Illuminate\Database\Eloquent\Collection;

class SiteVisitService
{
    public function getAllVisits(): Collection
    {
        return SiteVisit::with(['assignee', 'lead', 'client'])
            ->get();
    }

    public function getVisitById(string $id): SiteVisit
    {
        return SiteVisit::with(['assignee', 'lead', 'client'])
            ->where('id', $id)
            ->firstOrFail();
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
