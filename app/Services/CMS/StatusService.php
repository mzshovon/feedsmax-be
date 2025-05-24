<?php

namespace App\Services\CMS;

use App\Entity\StatusMapperResponseEntityForCMS;
use App\Repositories\StatusRepo;
use App\Services\Contracts\CMS\StatusServiceInterface;
use Illuminate\Database\Eloquent\Model;

class StatusService implements StatusServiceInterface
{
    public function __construct(
        private StatusRepo $statusRepo
    ) {
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = $this->statusRepo->getStatus();
        return $data;
    }

    /**
     * @return array
     */
    public function getStatusById(int $statusId): array
    {
        $data = $this->response($this->statusRepo->getStatusById($statusId));
        return $data;
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function store(array $request): bool
    {
        $storeData = $this->statusRepo->storeStatus($request);
        if ($storeData) {
            return true;
        }
        return false;
    }

    /**
     * @param array $request
     * @param int $statusId
     *
     * @return bool
     */
    public function update(array $request, int $statusId): bool
    {
        $fillableData = $this->fillableData($request);

        $updateData = $this->statusRepo->updateStatusById("id", $statusId, $fillableData);
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
        $fillable = ['user_name', 'id', 'group_id', 'name', 'description'];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @param int $statusId
     * @param array $request
     *
     * @return bool
     */
    public function delete(int $statusId, array $request): bool
    {
        return $this->statusRepo->deleteStatusById($statusId, $request);
    }

    /**
     * @param Model|null $status
     *
     * @return array
     */
    private function response(Model|null $status): array
    {
        $data = [];
        if ($status) {
            $data = (new StatusMapperResponseEntityForCMS())
                ->setName($status->name)
                ->setDescription($status->description)
                ->build();
        }

        return $data;
    }
}
