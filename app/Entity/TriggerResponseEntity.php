<?php

namespace App\Entity;

class TriggerResponseEntity
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
     * @param string $msisdn
     * @return $this
     */
    public function setMsisdn(string $msisdn): TriggerResponseEntity{
        $this->data['msisdn'] = $msisdn;
        return $this;
    }

    /**
     * @param string $groupName
     * @return $this
     */
    public function setGroupName(string $groupName): TriggerResponseEntity{
        $this->data['group_name'] = $groupName;
        return $this;
    }

    /**
     * @param string $uuid
     * @return $this
     */
    public function setUuid(string $uuid): TriggerResponseEntity{
        $this->data['uuid'] = $uuid;
        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url): TriggerResponseEntity{
        $this->data['url'] = $url;
        return $this;
    }

    /**
     * @param array|null $retry
     *
     * @return $this
     */
    public function setRetry(array|null $retry): TriggerResponseEntity{
        $this->data['retry'] = $retry;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}
