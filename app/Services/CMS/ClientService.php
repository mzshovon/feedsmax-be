<?php

namespace App\Services\CMS;

use App\Repositories\ClientRepo;
use App\Services\Contracts\CMS\ClientServiceInterface;
use Illuminate\Database\Eloquent\Model;

class ClientService implements ClientServiceInterface
{
    public function __construct(
        private ClientRepo $clientRepo
    ) {}

    /**
     * Get all clients with proper column selection and formatting
     * 
     * @param array $selectedColumns
     * @param int $perPage
     * @return array
     */
    public function get(array $selectedColumns = [], ?int $perPage = null): array
    {
        // Define default columns to return
        $defaultColumns = [
            'id',
            'company_tag',
            'company_name', 
            'contact_name',
            'email',
            'phone',
            'address',
            // 'client_key',
            // 'client_secret',
            'status',
            'created_at',
            'updated_at'
        ];

        // Use provided columns or defaults
        $columns = !empty($selectedColumns) ? $selectedColumns : $defaultColumns;
        
        // Get clients with selected columns
        $clients = $this->clientRepo->getClients('read', $columns);
        
        return [
            'data' => $clients,
            'total' => count($clients),
            'per_page' => $perPage ?? count($clients),
            'current_page' => 1
        ];
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
                'client_secret' => $this->maskClientSecret($client->client_secret),
                'subscription_package_name' => $client->subscriptions[0]->package->package_name,
                'auto_renewal_enabled' => $client->subscriptions[0]->is_auto_renew,
                'quota_for_usage' => $client->subscriptions[0]?->package?->usageLimits?->toArray(),
                'usage_tracking' => $client->usageTracking?->toArray(),
                'status' => $client->status,
                'created_at' => $client->created_at,
                'updated_at' => $client->updated_at,
            ];
        }

        return $data;
    }

    /**
     * Mask client secret by showing half as asterisks and half as actual string
     * 
     * @param string|null $clientSecret
     * @return string
     */
    private function maskClientSecret(?string $clientSecret): string
    {
        if (!$clientSecret) {
            return '';
        }

        $length = strlen($clientSecret);
        $halfLength = (int) ceil($length / 2);
        
        $visiblePart = substr($clientSecret, $halfLength,0);
        $hiddenPart = str_repeat('*', $length - $halfLength);
        
        return $visiblePart . $hiddenPart;
    }
} 