<?php

namespace App\Ship\Core\Abstracts\Dto;

/**
 * @phpstan-template TArray of array<string, mixed>
 */
abstract readonly class DtoCore
{
    /**
     * @return TArray
     */
    public function toArray(): array
    {
        $array = [];

        $objectProperties = get_object_vars($this);
        foreach ($objectProperties as $property => $value) {
            $valueIsNull = is_null($value);
            if ($valueIsNull) {
                continue;
            }
            $array[$property] = $value;
        }
        /** @var TArray $array */
        return $array;
    }
}
