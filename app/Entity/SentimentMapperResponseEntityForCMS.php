<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Collection;

class SentimentMapperResponseEntityForCMS
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
     * @param string $keyword
     * @return $this
     */
    public function setKeyword(string $keyword): SentimentMapperResponseEntityForCMS{
        $this->data['keyword'] = $keyword;
        return $this;
    }

    /**
     * @param string $children
     *
     * @return $this
     */
    public function setCategory(string $category): SentimentMapperResponseEntityForCMS{
        $this->data['category'] = $category;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}
