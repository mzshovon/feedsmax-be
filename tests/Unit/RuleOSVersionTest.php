<?php

namespace Tests\Unit;

use App\Enums\Comparator;
use App\Services\PolicyService\TargetByAppVersion;
use App\Services\PolicyService\TargetByOSVersion;
use PHPUnit\Framework\TestCase;

class RuleOSVersionTest extends TestCase
{
    private TargetByOSVersion $targetByOSVersion;

    protected function setUp(): void
    {
        parent::setUp();
        $this->targetByOSVersion = new TargetByOSVersion();
    }

    /**
     * App version rule check
     * @dataProvider dataProviderForRuleForOSVersionsTesting
     * @param $request_version
     * @param $args
     * @param $assert
     */
    public function testRuleForAppVersions($request_version, $args, $assert): void
    {
        $match_status = $this->targetByOSVersion->match(
            [
                'OSVersion' => $request_version
            ],
            $args
        );

        $this->assertTrue($assert == $match_status);
    }

    /**
     * @return array[]
     */
    public static function dataProviderForRuleForOSVersionsTesting(): array
    {
        return
            [
                [
                    'request_version' => "10.11.1",
                    'args' => ["10.12.2", 1, Comparator::Equal->value],
                    'assert' => false
                ],
                [
                    'request_version' => "10.11.1",
                    'args' => ["10.11.1", 1, Comparator::GreaterThanEqual->value],
                    'assert' => true
                ],
                [
                    'request_version' => "10.11.1",
                    'args' => ["10.1.2", 1, Comparator::LessThanEqual->value],
                    'assert' => false
                ],
            ];
    }
}
