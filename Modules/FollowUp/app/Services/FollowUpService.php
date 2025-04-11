<?php

namespace Modules\FollowUp\Services;

use Modules\FollowUp\Models\FollowUp;
use Illuminate\Pagination\LengthAwarePaginator;


class FollowUpService
{
    public function getAllFollowUps()
    {
        return FollowUp::with([ 'creator', 'updater'])
            ->latest();
    }

    public function getPaginatedFollowUps(
        int $perPage = 15,
        bool $withTrashed = false,
        ?string $status = null,
        ?string $clientId = null,
        ?string $leadId = null,
        ?string $createdBy = null,
        ?string $lastUpdatedBy = null
    ): LengthAwarePaginator
    {
        $query = FollowUp::query()->with([ 'creator', 'updater']);
        
        if ($withTrashed) {
            $query->withTrashed();
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($clientId) {
            $query->where('client_id', $clientId);
        }
        
        if ($leadId) {
            $query->where('lead_id', $leadId);
        }
        
        if ($createdBy) {
            $query->where('created_by', $createdBy);
        }
        
        if ($lastUpdatedBy) {
            $query->where('last_updated_by', $lastUpdatedBy);
        }
        
        return $query->latest()->paginate($perPage);
    }

    public function getFollowUpById(string $id)
    {
        return FollowUp::with([ 'creator', 'updater'])
            ->where('id', $id)
            ->firstOrFail();
    }

    public function createFollowUp(array $data)
    {
        return FollowUp::create($data);
    }

    public function updateFollowUp(string $id, array $data)
    {
        $data['last_updated_by'] = auth()->user()->uuid; // or auth()->user()->id
        $followUp = $this->getFollowUpById($id);
        $followUp->update($data);
        return $followUp->fresh();
    }

    public function deleteFollowUp(string $id)
    {
        $followUp = $this->getFollowUpById($id);
        $followUp->delete();
        return $followUp;
    }
}
