<?php

namespace App\Services\CMS;

use App\Entity\ChannelResponseEntityForCMS;
use App\Repositories\RulesRepo;
use App\Services\Contracts\CMS\RuleServiceInterface;
use Illuminate\Database\Eloquent\Model;

class RuleService implements RuleServiceInterface
{
    public function __construct(
        private RulesRepo $rulesRepo
    ) {
    }

    /**
     * @return array
     */
    public function getRulesNameForSelection(): array
    {
        $data = $this->rulesRepo->ruleGetByFuncNameForAttach();
        return $data;
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
        $updateData = $this->rulesRepo->updateRuleById("id", $request['id'], $request);
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
        return $this->rulesRepo->deleteRuleById($ruleId, $request);
    }
}
