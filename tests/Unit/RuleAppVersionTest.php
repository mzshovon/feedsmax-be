<?php

namespace Tests\Unit;

use App\Enums\Comparator;
use App\Services\RuleService\TargetByAppVersion;
use PHPUnit\Framework\TestCase;

class RuleAppVersionTest extends TestCase
{
    private TargetByAppVersion $targetByAppVersion;

    protected function setUp(): void
    {
        parent::setUp();
        $this->targetByAppVersion = new TargetByAppVersion();
    }

    /**
     * App version rule check
     * @dataProvider dataProviderForRuleForAppVersionsTesting
     * @param $request_version
     * @param $args
     * @param $assert
     */
    public function testRuleForAppVersions($request_version, $args, $assert): void
    {
        $match_status = $this->targetByAppVersion->match(
            [
                'appVersion' => $request_version
            ],
            $args
        );

        $this->assertTrue($assert == $match_status);
    }

    /**
     * @return array[]
     */
    public static function dataProviderForRuleForAppVersionsTesting(): array
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
