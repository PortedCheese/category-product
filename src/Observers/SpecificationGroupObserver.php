<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\SpecificationGroup;
use PortedCheese\BaseSettings\Exceptions\PreventDeleteException;

class SpecificationGroupObserver
{
    /**
     * Перед удалением.
     *
     * @param SpecificationGroup $group
     * @throws PreventDeleteException
     */
    public function deleting(SpecificationGroup $group)
    {
        if ($group->specifications->count()) {
            throw new PreventDeleteException("Невозможно удалить, есть поля относящиеся к данной группе");
        }
    }
}
