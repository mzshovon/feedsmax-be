<?php

namespace App\Services;

use App\Enums\ChoiceType;
use App\Models\Status;
use App\Repositories\StriveRepo;
use App\Repositories\BucketRepo;
use App\Repositories\ResponseRepo;

class ResponseService
{
    const OPEN_STATUS = "open";

    public function __construct(
        private ResponseRepo $repo,
        private StriveRepo $StriveRepo,
        private BucketRepo $groupRepo,
    ) {
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function store(array $request): bool
    {
        try {
            $responses = isset($request['responses']) ? $this->buildResponseFormat($request) : [];
            $storeResponse = $this->repo->store($responses);
            if($storeResponse) {
                $dataForUpdate = $this->attemptUpdateDataBuilder($request);
                $this->StriveRepo->update('id', $request['attemptId'], $dataForUpdate);
            }
            return $storeResponse;
        } catch (\Exception $ex) {
            throw new \Exception("Response Store Exception"); // Wasn't added before
        }

    }

    /**
     * @param array $request
     *
     * @return array
     */
    private function buildResponseFormat(array $request) : array
    {
        $attemptId = $request['attemptId'];
        $msisdn = formatMsisdnInLocal($request['msisdn']);
        $responses = $request['responses'];
        array_walk($responses, function(&$response) use ($attemptId, $msisdn){
            $response['attempt_id'] = $attemptId;
            $response['msisdn'] = $msisdn;
            $response['response_flat'] = implode(",",array_column($response['choice'], 'value'));
            $response['response_object'] = json_encode($response['choice']);
            $response['created_at'] = now();
            $response['updated_at'] = now();
            unset($response['choice']);
            unset($response['selection_type']);
        });
        return $responses;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    private function attemptUpdateDataBuilder(array $request) : array
    {
        $data = [];
        $data['status_id'] = Status::where("name", self::OPEN_STATUS)->first()->id ?? null;
        $data['lang'] = $request['lang'];
        $data['submitted_at'] = now();
        $data['nps_score'] = $request['nps_score'];
        if(isset($request['msisdn_num'])) {
            $data['msisdn'] = formatMsisdnInLocal($request['msisdn']);
        }

        return $data;
    }

}
