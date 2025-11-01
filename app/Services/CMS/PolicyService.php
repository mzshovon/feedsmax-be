<?php

namespace App\Services\CMS;

use App\Entity\PolicyResponseEntityForCMS;
use App\Repositories\PolicyRepo;
use App\Services\Contracts\CMS\PolicyServiceInterface;
use Illuminate\Database\Eloquent\Model;

class PolicyService implements PolicyServiceInterface
{
    public function __construct(
        private PolicyRepo $policyRepo
    ) {
    }

    /**
     * @return array
     */
    public function getPolicyList(?string $columns = null): array
    {
        $columns = !empty($columns) ? explode(",", $columns) : ["*"];
        $data = $this->policyRepo->getPolicies($columns);
        return $data;
    }

    /**
     * @return array
     */
    public function getPolicyById(int $policyId): array
    {
        $data = $this->response($this->policyRepo->getPolicyById($policyId));
        return $data;
    }

    public function store(array $request): bool
    {
        $storeData = $this->policyRepo->storePolicy($request);
        if ($storeData) {
            return true;
        }
        return false;
    }

    /**
     * @param int $ruleId
     * @param array $request
     *
     * @return bool
     */
    public function update(array $request): bool
    {
        $fillableData = $this->fillableData($request);

        if(array_key_exists('args', $fillableData)) {
            $fillableData['args'] = json_encode($fillableData['args']);
        }
        $updateData = $this->policyRepo->updatePolicyById("id", $request['id'], $request);
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
        $fillable = ['user_name', 'id', 'name', 'func', 'args', 'editable', 'definition', 'enabled'];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @param int $ruleId
     * @param array $request
     *
     * @return bool
     */
    public function delete(int $ruleId, array $request): bool
    {
        return $this->policyRepo->deletePolicyById($ruleId, $request);
    }

        /**
     * @param Model|null $policy
     *
     * @return array
     */
    private function response(Model|null $policy): array
    {
        $data = [];
        if ($policy) {
            $data = (new PolicyResponseEntityForCMS())
                ->setName($policy->name)
                ->setCallObjectNotation($policy->call_object_notation)
                ->setOrder($policy->order)
                ->setArgs($policy->args)
                ->setStatus($policy->status)
                ->setUpdateParams($policy?->update_params)
                ->setDefinition($policy->definition)
                ->build();
        }

        return $data;
    }
}
