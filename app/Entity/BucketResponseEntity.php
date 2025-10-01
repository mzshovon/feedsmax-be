<?php

namespace App\Entity;

class BucketResponseEntity
{
    /**
     * @var array
     */
    private array $data;

    public function __construct()
    {
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): BucketResponseEntity{
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(string|null $description): BucketResponseEntity{
        $this->data['description'] = $description;
        return $this;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): BucketResponseEntity{
        $this->data['status'] = $status;
        return $this;
    }

    /**
     * @param string $quota
     *
     * @return $this
     */
    public function setQuota(int|null $quota): BucketResponseEntity{
        $this->data['quota'] = $quota;
        return $this;
    }

    /**
     * @param string $served
     *
     * @return $this
     */
    public function setServed(int|null $served): BucketResponseEntity{
        $this->data['served'] = $served;
        return $this;
    }

    /**
     * @param int $nps_ques_id
     *
     * @return $this
     */
    public function npsQuestionId(int|null $nps_ques_id): BucketResponseEntity{
        $this->data['nps_ques_id'] = $nps_ques_id;
        return $this;
    }

    /**
     * @param int $promoter_range
     *
     * @return $this
     */
    public function promoterRange(int|null $promoter_range): BucketResponseEntity{
        $this->data['promoter_range'] = $promoter_range;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}
