<?php

namespace App\Services\RuleService;

use App\Enums\Comparator;
use App\Services\Contracts\RuleEngineInterface;

class TargetByAppVersion implements RuleEngineInterface
{

    private int $pad = 2;
    private string $pad_value = "0";

    public function match(array $request, ...$args): bool
    {
        [$version, $comparator] = $args[0];
        if(isset($request['appVersion'])){
            $request_app_version = intval($this->fullVersion($request['appVersion']));
            $rule_app_version = intval($this->fullVersion($version));
            switch ($comparator){
                case Comparator::Equal->value:
                    if($request_app_version == $rule_app_version){
                        return true;
                    }
                    break;
                case Comparator::GreaterThan->value:
                    if($request_app_version > $rule_app_version){
                        return true;
                    }
                    break;
                case Comparator::GreaterThanEqual->value:
                    if($request_app_version >= $rule_app_version){
                        return true;
                    }
                    break;
                case Comparator::LessThan->value:
                    if($request_app_version < $rule_app_version){
                        return true;
                    }
                    break;
                case Comparator::LessThanEqual->value:
                    if($request_app_version <= $rule_app_version){
                        return true;
                    }
                    break;
                default:
                    return false;
            }
        }
        return false;
    }

    private function fullVersion($version): string{
        $full_version = "";
        $values = explode('.',$version);
        foreach ($values as $value){
            $full_version .= str_pad($value,$this->pad, $this->pad_value, STR_PAD_LEFT);
        }
        return $full_version;
    }
}
