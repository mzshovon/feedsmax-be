<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Collection;

class SectionResponseEntity
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
    public function setName(string $name): SectionResponseEntity{
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param int|null $set_no
     *
     * @return $this
     */
    public function setNo(int|null $set_no): SectionResponseEntity{
        $this->data['set_no'] = $set_no;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}
