<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Collection;

class StatusMapperResponseEntityForCMS
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
    public function setName(string $name): StatusMapperResponseEntityForCMS{
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(string|null $description): StatusMapperResponseEntityForCMS{
        $this->data['description'] = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}
