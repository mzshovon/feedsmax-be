<?php

namespace App\Services\CMS;

use App\Entity\SentimentMapperResponseEntityForCMS;
use App\Repositories\SentimentRepo;
use App\Services\Contracts\CMS\SentimentMapperServiceInterface;
use Illuminate\Database\Eloquent\Model;

class SentimentMapperService implements SentimentMapperServiceInterface
{
    public function __construct(
        private SentimentRepo $sentimentRepo
    ) {
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = $this->sentimentRepo->getSentiment();
        return $data;
    }

    /**
     * @return array
     */
    public function getListOfSentimentCategories(): array
    {
        return getSentimentCaterories() ?? [];
    }

    /**
     * @param int $sentimentId
     *
     * @return array
     */
    public function getSentimentRecordById(int $sentimentId): array
    {
        $data = $this->response($this->sentimentRepo->getSentimentById($sentimentId));
        return $data;
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function store(array $request): bool
    {
        $category = $request['sentiment_category'];
        $formattedRequest = array_map(function($value) use($category){
            return [
                "sentiment_category" => $category,
                "keywords" => $value
            ];
        }, $request['keywords']);

        $storeData = $this->sentimentRepo->storeSentiment($formattedRequest);
        if ($storeData) {
            return true;
        }
        return false;
    }

    /**
     * @param array $request
     * @param int $sentimentId
     *
     * @return bool
     */
    public function update(array $request, int $sentimentId): bool
    {
        $updateData = $this->sentimentRepo->updateSentimentById("id", $sentimentId, $request);
        if ($updateData) {
            return true;
        }
        return false;
    }

    /**
     * @param int $sentimentId
     *
     * @return bool
     */
    public function delete(int $sentimentId): bool
    {
        return $this->sentimentRepo->deleteSentimentById($sentimentId);
    }

    /**
     * @param Model|null $sentiment
     *
     * @return array
     */
    private function response(Model|null $sentiment): array
    {
        $data = [];
        if ($sentiment) {
            $data = (new SentimentMapperResponseEntityForCMS())
                ->setKeyword($sentiment->keywords)
                ->setCategory($sentiment->sentiment_category)
                ->build();
        }

        return $data;
    }
}
