<?php

namespace App\Entity;

class GroupResponseEntity
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
    public function setName(string $name): GroupResponseEntity{
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(string|null $description): GroupResponseEntity{
        $this->data['description'] = $description;
        return $this;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): GroupResponseEntity{
        $this->data['status'] = $status;
        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string|null $type): GroupResponseEntity{
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * @param int $nps_ques_id
     *
     * @return $this
     */
    public function npsQuestionId(int|null $nps_ques_id): GroupResponseEntity{
        $this->data['nps_ques_id'] = $nps_ques_id;
        return $this;
    }

    /**
     * @param int $promoter_range
     *
     * @return $this
     */
    public function promoterRange(int|null $promoter_range): GroupResponseEntity{
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
