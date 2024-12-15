<?php

namespace App\Ship\Core\Abstracts\Presenters;

use App\Ship\Core\Abstracts\Models\ModelCore;
use JetBrains\PhpStorm\Pure;
use stdClass;

abstract class PresenterCore
{
    /**
     * Gets a model and returns a stdClass
     * that has all the public properties of the passed model set.
     *
     * @param ModelCore $model
     * @return stdClass Returns stdClass in which all public
     * properties of the passed model have been set
     */
    #[Pure]
    public function presentPublicProperties(ModelCore $model): stdClass
    {
        $object = new stdClass();

        // gets array of properties and their values
        $properties = get_object_vars($model);

        // sets property values to a stdClass object
        foreach ($properties as $property => $value) {
            $object->{$property} = $value;
        }

        return $object;
    }
}
