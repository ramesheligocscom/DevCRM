<?php

namespace Modules\FollowUp\Services;

use App\Modules\FollowUp\Models\FollowUp;

class FollowUpService
{
    public function getAllFollowUps()
    {
        return FollowUp::with(['lead', 'client', 'creator', 'updater'])
            ->latest()
            ->get();
    }

    public function getFollowUpById(string $id)
    {
        return FollowUp::with(['lead', 'client', 'creator', 'updater'])
            ->where('id', $id)
            ->firstOrFail();
    }

    public function createFollowUp(array $data)
    {
        return FollowUp::create($data);
    }

    public function updateFollowUp(string $id, array $data)
    {
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
