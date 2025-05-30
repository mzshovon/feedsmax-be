<?php

namespace App\Services\RuleService;

use App\Models\Rule;
use App\Repositories\RulesRepo;
use App\Services\Contracts\RuleEngineInterface;
use App\Services\RuleService\SurveySessionCustomer;
use Exception;
use Reflection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

class RulesMediator
{
    private int $trigger_id;

    private RulesRepo $rulesRepo;

    public function __construct(
        int $trigger_id
    ) {
        $this->trigger_id = $trigger_id;
        $this->rulesRepo = new RulesRepo(new Rule());
    }

    /**
     * @throws Exception
     */
    public function match(array $request, int $trigger_id, int $channel_id): array
    {
        $matched = [];
        $request['trigger_id'] = $trigger_id;
        $request['channel_id'] = $channel_id;
        try {
            $rules = $this->rulesRepo->rules($trigger_id, 'read');

            if(count($rules) > 0){
                foreach ($rules as $key => $rule) {
                    [$method, $object] = $this->getClassObject($rule['func']);
                    $args = json_decode($rule['args'], true);
                    $match = $method->invokeArgs(new $object, [$request, $args]);
                    if ($match) {
                        $matched[] = $rule['func'];
                    } else {
                        $matched = [];
                        break;
                    }
                }
                return $matched;
            }else{
                throw new Exception("No rules attached");
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * @param string $rule
     * @return array
     * @throws ReflectionException
     * @throws Exception
     */
    private function getClassObject(string $rule): array
    {
        try {
            $addSpace = str_replace('_', ' ', $rule);
            $upperCase = ucwords($addSpace);
            $className = str_replace(' ', '', $upperCase);
            $callingClass = '\\App\\Services\\RuleService\\' . $className;
            return [new ReflectionMethod($callingClass, 'match'), $callingClass];
        } catch (ReflectionException $ex) {
            throw new Exception($ex->getMessage());
        }
    }
}
