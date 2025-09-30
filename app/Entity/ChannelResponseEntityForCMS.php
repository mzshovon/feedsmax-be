<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class ChannelResponseEntityForCMS
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
     * @param string $tag
     * @return $this
     */
    public function setTag(string $tag): ChannelResponseEntityForCMS{
        $this->data['tag'] = $tag;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): ChannelResponseEntityForCMS{
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param string $app_key
     * @return $this
     */
    public function setAppKey(string|null $app_key): ChannelResponseEntityForCMS{
        $this->data['app_key'] = $app_key;
        return $this;
    }

    /**
     * @param array $app_secret
     *
     * @return $this
     */
    public function setAppSecret(string|null $app_secret): ChannelResponseEntityForCMS{
        $this->data['app_secret'] = $app_secret;
        return $this;
    }
    /**
     * @param array|null $jwks
     *
     * @return $this
     */
    public function setJWKS(string|null $jwks): ChannelResponseEntityForCMS{
        $this->data['jwks'] = $jwks;
        return $this;
    }

    /**
     * @param array|null $status
     *
     * @return $this
     */
    public function setStatus(int $status): ChannelResponseEntityForCMS{
        $this->data['status'] = $status;
        return $this;
    }

    /**
     * @param int $pagination
     * @return $this
     */
    public function setPagination(int $pagination): ChannelResponseEntityForCMS{
        $this->data['pagination'] = $pagination;
        return $this;
    }

    /**
     * @param Model|null $theme
     * @return $this
     */
    public function setTheme(Model|null $theme): ChannelResponseEntityForCMS{
        $this->data['theme'] = $theme;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}
