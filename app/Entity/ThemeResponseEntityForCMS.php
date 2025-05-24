<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Collection;

class ThemeResponseEntityForCMS
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
    public function setName(string $name): ThemeResponseEntityForCMS{
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue(string $value): ThemeResponseEntityForCMS{
        $this->data['value'] = $value;
        return $this;
    }

    /**
     * @param Collection $channels
     *
     * @return $this
     */
    public function setChannels(Collection $channels): ThemeResponseEntityForCMS{
        $this->data['channels'] = $channels;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}
