<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class EventResponseEntityForCMS
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
     * @param string $event_type
     * @return $this
     */
    public function setEventType(string $event_type): EventResponseEntityForCMS
    {
        $this->data['type'] = $event_type;
        return $this;
    }

    /**
     * @param string $event_name
     * @return $this
     */
    public function setEventName(string $event_name): EventResponseEntityForCMS
    {
        $this->data['event'] = $event_name;
        return $this;
    }

    /**
     * @param int $bucket_id
     * @return $this
     */
    public function setBucketId(int $bucket_id): EventResponseEntityForCMS
    {
        $this->data['bucket_id'] = $bucket_id;
        return $this;
    }

    /**
     * @param string $context
     * @return $this
     */
    public function setContext(string $context): EventResponseEntityForCMS
    {
        $this->data['context'] = $context;
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): EventResponseEntityForCMS
    {
        $this->data['status'] = $status;
        return $this;
    }

    /**
     * @param array $description
     *
     * @return $this
     */
    public function setDescription(string|null $description): EventResponseEntityForCMS
    {
        $this->data['description'] = $description;
        return $this;
    }
    /**
     * @param Model $channel
     *
     * @return $this
     */
    public function setChannel(Model|null $channel): EventResponseEntityForCMS
    {
        $this->data['channel'] = $channel;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array
    {
        return $this->data;
    }
}
