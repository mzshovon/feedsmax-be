<?php

namespace App\Repositories;

use App\Models\Redirection;
use App\Services\LoggerService;
use Exception;
use Illuminate\Database\Eloquent\Model;

class RedirectionRepo
{
    public Model $model;

    public function __construct(Redirection $model, private readonly ChannelRepo $channelRepo)
    {
        $this->model = $model;
    }

    /**
     * @param int $attemptId
     * @param string $channelTag
     * @param string $instance_type
     *
     * @return string
     */
    public function getRedirectionLinkByAttemptId(int $attemptId, string $channelTag, $instance_type = "read"): string
    {
        try {
            $redirection_link = "";
            $redirection_data = $this->model::on('mysql::' . $instance_type)
                ->where("attempt_id", $attemptId)
                ->first();
            if($redirection_data) {
                $redirection_link = $redirection_data->redirection_link;
            } else {
               $channel_data = $this->channelRepo->getInfoByChannelTag($channelTag);
               $redirection_link = $channel_data['redirection_link'] ?? "";
            }
            return $redirection_link;
        } catch (\Exception $e) {
            $loggerService = app(LoggerService::class);
            $loggerService->exception($e->getMessage());
            throw new Exception("Database error");
        }

    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeRedirectionData(array $request) : Model
    {
        try {
            $store = $this->model::on('mysql::write')->create($request);
            return $store;

        } catch (\Exception $e) {
            throw new Exception("Database connectivity issue");
        }
    }
}
