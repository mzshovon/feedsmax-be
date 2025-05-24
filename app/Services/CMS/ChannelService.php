<?php

namespace App\Services\CMS;

use App\Entity\ChannelResponseEntityForCMS;
use App\Repositories\ChannelRepo;
use App\Services\Contracts\CMS\ChannelServiceInterface;
use Illuminate\Database\Eloquent\Model;

class ChannelService implements ChannelServiceInterface
{
    public function __construct(
        private ChannelRepo $channelRepo
    ) {
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = $this->channelRepo->getChannels();
        return $data;
    }

    /**
     * @return array
     */
    public function getChannelById(int $channelId): array
    {
        $data = $this->response($this->channelRepo->getChannelById($channelId));
        return $data;
    }

    public function store(array $request): bool
    {
        $storeData = $this->channelRepo->storeChannel($request);
        if ($storeData) {
            return true;
        }
        return false;
    }

    public function update(array $request, int $channelId): bool
    {
        $fillableData = $this->fillableData($request);

        $updateData = $this->channelRepo->updateChannelById("id", $channelId, $fillableData);
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
        $fillable = ['user_name', 'name', 'app_key', 'app_secret', 'status', 'num_of_questions', 'theme', 'retry'];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @param int $channelId
     * @param array $request
     *
     * @return bool
     */
    public function delete(int $channelId, array $request): bool
    {
        return $this->channelRepo->deleteChannelById($channelId, $request);
    }

    /**
     * @param Model|null $channel
     *
     * @return array
     */
    private function response(Model|null $channel): array
    {
        $data = [];
        if ($channel) {
            $data = (new ChannelResponseEntityForCMS())
                ->setTag($channel->tag)
                ->setName($channel->name)
                ->setAppKey($channel->app_key)
                ->setAppSecret($channel->app_secret)
                ->setJWKS($channel->jwks)
                ->setStatus($channel->status)
                ->setNumOfQuestions($channel->num_of_questions)
                ->setTheme($channel->themes)
                ->build();
        }

        return $data;
    }
}
