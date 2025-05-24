<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Collection;

class CategorySubCategoryResponseEntityForCMS
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
    public function setName(string $name): CategorySubCategoryResponseEntityForCMS{
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param Collection|null $children
     *
     * @return $this
     */
    public function setChildren(Collection|null $children): CategorySubCategoryResponseEntityForCMS{
        $this->data['children'] = $children;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}
