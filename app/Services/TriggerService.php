<?php

namespace App\Services;

use App\Classes\CryptoTokenManager;
use App\Entity\TriggerResponseEntity;
use App\Enums\QuotaType;
use App\Repositories\HitRepo;
use App\Repositories\RedirectionRepo;
use App\Repositories\TriggerRepo;
use App\Services\Contracts\TriggerServiceInterface;
use App\Services\RuleService\RulesMediator;
use App\Services\StrategyService\LocationQuotaCheckStrategy;
use App\Services\StrategyService\QuotaChecker;
use App\Services\StrategyService\SessionQuotaCheckStrategy;
use DateTime;
use Exception;
use Illuminate\Support\Str;

class TriggerService implements TriggerServiceInterface
{
    private string $version = 'v1';

    public function __construct(
        private readonly HitService $hitService,
        private readonly AttemptService $attemptService,
        public TriggerRepo $triggerRepo,
        public HitRepo $hitRepo,
        public RedirectionRepo $redirectionRepo
    ) {
    }

    /**
     * @param string $channel
     * @param string $event
     * @param array $request
     *
     * @return array
     * @throws Exception
     */
    public function trigger(string $channel, string $event, array $request): array
    {
        $uuid = null;

        $msisdn = formatMsisdnInLocal($request['msisdn']);

        [$triggerId, $lang, $channelId, $retry, $groupId, $nextGroupId, $groupName] = $this->triggerRepo->getTriggerInfo($event, $channel, 'read');

        try{
            if (!is_null($triggerId) && !is_null($channelId) && !is_null($groupId)) {

                $matchedRules = (new RulesMediator($triggerId))->match($request, $triggerId, $channelId);
                $attemptId = null;
                if (!empty($matchedRules)) {
                    // If rule matches, data will be stored in attempts
                    [$attemptId, $attemptObject] = $this->attemptService->store(
                        $request,
                        $channel,
                        $channelId,
                        $event,
                        $triggerId,
                        $groupId,
                        $matchedRules,
                        $nextGroupId
                    );
                    if($attemptObject->groups) {
                        $groupName = $attemptObject->groups->name;
                    }

                    if(isset($request['redirection_link'])) {
                        //* Redirection data wrapper
                        $redirection_data = $this->redirectionDataBuilder($attemptId, $request['redirection_link']);
                        $this->redirectionRepo->storeRedirectionData($redirection_data);
                    }

                    $crypto = new CryptoTokenManager();
                    $uuid = $crypto->encrypt(
                        [$attemptId, $channel],
                        new DateTime("now +2 hour"),
                        $msisdn
                    );
                }
                //! Hit store is paused now
                // $this->hitService->store($msisdn, $triggerId, $channelId, $attemptId);

            }
            return $this->response(
                $uuid,
                $msisdn,
                json_decode($retry, true) ?? null,
                $channel,
                $event,
                $groupName,
                isset($request['lang']) ? $request['lang'] : $lang,
                !empty($matchedRules)
            );
        }catch (Exception $ex){
            throw new Exception("Rule matching exception");
        }
    }

    /**
     * @param string|null $uuid
     * @param string $msisdn
     * @param array|null $retry
     * @param string $channel
     * @param string $event
     * @param string $groupName
     * @param bool $matchedRule
     *
     * @return array
     */
    private function response(
        string|null $uuid,
        string $msisdn,
        array|null $retry,
        string $channel,
        string $event,
        ?string $groupName,
        ?string $lang,
        bool $matchedRule = false
    ): array
    {
        $data = [];
        $data['match'] = false; // match flag
        $data['data'] = [
            "msisdn" => $msisdn,
            "uuid" => null,
            "url" => null
        ];
        if ($matchedRule) {
            $surveyWebViewURL = config('app.survey_url');
            $data['match'] = true;
            $data['data'] = (new TriggerResponseEntity())
                ->setMsisdn($msisdn)
                ->setGroupName($groupName)
                ->setUuid($uuid)
                ->setUrl($this->generateUrlForWebview(
                    $surveyWebViewURL,
                    $this->version,
                    $channel,
                    $event,
                    $uuid,
                    $lang,
                    // $msisdn
                    ))
                ->setRetry($retry)
                ->build();
        }

        return $data;
    }

    /**
     * @param string $surveyWebViewURL
     * @param string $version
     * @param string $channel
     * @param string $event
     * @param string $uuid
     * @param string $lang
     *
     * @return string
     */
    private function generateUrlForWebview(
        string $surveyWebViewURL,
        string $version,
        string $channel,
        string $event,
        string $uuid,
        string $lang,
        // string $msisdn,
        ): string
    {
        $url = $surveyWebViewURL . '/' . $version . '/' . $channel . '/' . $event . '/' . $uuid ."?";
        $params = [
            "header" => 0,
            "lang" => $lang
        ] + (str_contains($event, "guest") ?[
            // "_msisdn" => $msisdn,
            "required" => "nps, msisdn"
        ] : []);
        return  $url . http_build_query($params);
    }

    /**
     * @param int $attemptId
     * @param string|null $redirectionLink
     *
     * @return array
     */
    private function redirectionDataBuilder(int $attemptId, ?string $redirectionLink): array
    {
        $data = [];
        $data['attempt_id'] = $attemptId;
        $data['redirection_link'] = $redirectionLink;
        return $data;
    }
}
