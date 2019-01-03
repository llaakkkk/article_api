<?php

namespace App\Service\UseCase;

class UseCaseRequest
{
    /**
     * @param array $data
     * @param bool  $onlyDefined
     *
     * @return $this
     */
    public function populate(array $data, $onlyDefined = false)
    {
        foreach ($data as $name => $value) {
            if (!property_exists($this, $name)) {
                continue;
            }

            if ($onlyDefined && ($value === null || $value === '')) {
                continue;
            }

            $this->$name = $value;
        }

        return $this;
    }
}
