<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class PolicyResponseEntityForCMS
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
    public function setName(string $name): PolicyResponseEntityForCMS{
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param string $call_object_notation
     * @return $this
     */
    public function setCallObjectNotation(string $call_object_notation): PolicyResponseEntityForCMS{
        $this->data['call_object_notation'] = $call_object_notation;
        return $this;
    }

    /**
     * @param int $order
     * @return $this
     */
    public function setOrder(int $order): PolicyResponseEntityForCMS{
        $this->data['order'] = $order;
        return $this;
    }

    /**
     * @param array $args
     *
     * @return $this
     */
    public function setArgs(array $args): PolicyResponseEntityForCMS{
        $this->data['args'] = $args;
        return $this;
    }
    /**
     * @param array $update_params
     *
     * @return $this
     */
    public function setUpdateParams(array $update_params): PolicyResponseEntityForCMS{
        $this->data['update_params'] = $update_params;
        return $this;
    }

    /**
     * @param string|null $definition
     * 
     * @return $this
     */
    public function setDefinition(?string $definition): PolicyResponseEntityForCMS{
        $this->data['definition'] = $definition;
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): PolicyResponseEntityForCMS{
        $this->data['status'] = $status;
        return $this;
    }
    
    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}
