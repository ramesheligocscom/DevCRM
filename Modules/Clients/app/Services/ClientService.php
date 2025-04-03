<?php

namespace Modules\Clients\Services;

// use Illuminate\Support\Facades\Auth;
use Modules\Clients\app\Models\Client;

class ClientService
{
    public function createClient(array $data): Client
    {
        return Client::create([
            'id' => Str::uuid(),
            ...$data,
            'created_by' => Auth::id(),
        ]);
    }

    public function updateClient(Client $Client, array $data): Client
    {
        $Client->update([
            ...$data,
            'last_updated_by' => Auth::id(),
            'last_updated_at' => now(),
        ]);

        return $Client->fresh();
    }

    public function deleteClient(Client $Client): void
    {
        $Client->update([
            'is_deleted' => true,
            'last_updated_by' => Auth::id(),
            'last_updated_at' => now(),
        ]);
    }
}
