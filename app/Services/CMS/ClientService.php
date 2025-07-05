<?php

namespace App\Services\CMS;

use App\Repositories\ClientRepo;
use App\Services\Contracts\CMS\ClientServiceInterface;
use Illuminate\Database\Eloquent\Model;

class ClientService implements ClientServiceInterface
{
    public function __construct(
        private ClientRepo $clientRepo
    ) {
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = $this->clientRepo->getClients();
        return $data;
    }

    /**
     * @return array
     */
    public function getClientById(int $clientId): array
    {
        $data = $this->response($this->clientRepo->getClientById($clientId));
        return $data;
    }

    public function store(array $request): bool
    {
        $storeData = $this->clientRepo->storeClient($request);
        if ($storeData) {
            return true;
        }
        return false;
    }

    public function update(array $request, int $clientId): bool
    {
        $fillableData = $this->fillableData($request);

        $updateData = $this->clientRepo->updateClientById("id", $clientId, $fillableData);
        if ($updateData) {
            return true;
        }
        return false;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    private function fillableData(array $request): array{
        $data = [];
        $fillable = [
            'company_tag', 
            'company_name', 
            'contact_name', 
            'email', 
            'phone', 
            'address', 
            'client_key', 
            'client_secret', 
            'status',
            'subscriptions'
        ];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @param int $clientId
     * @param array $request
     *
     * @return bool
     */
    public function delete(int $clientId, array $request): bool
    {
        return $this->clientRepo->deleteClientById($clientId, $request);
    }

    /**
     * @param Model|null $client
     *
     * @return array
     */
    private function response(Model|null $client): array
    {
        $data = [];
        if ($client) {
            $data = [
                'id' => $client->id,
                'company_tag' => $client->company_tag,
                'company_name' => $client->company_name,
                'contact_name' => $client->contact_name,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
                'client_key' => $client->client_key,
                'subscription_package_name' => $client->subscriptions[0]->package->package_name,
                'auto_renewal_enabled' => $client->subscriptions[0]->is_auto_renew,
                'quota_for_usage' => $client->subscriptions[0]?->package?->usageLimits?->toArray(),
                'usage_tracking' => $client->usageTracking?->toArray(),
                // 'client_secret' => $client->client_secret,
                'status' => $client->status,
                'created_at' => $client->created_at,
                'updated_at' => $client->updated_at,
            ];
        }

        return $data;
    }
} 