<?php

namespace App\Http\Admin\Data;

use Symfony\Component\Form\FormTypeInterface;

interface CrudDataInterface
{

    public function getEntity(): object;

    /**
     * @return class-string<FormTypeInterface<\App\Http\Admin\Data\CrudDataInterface>>
     */
    public function getFormObject(): string;

    public function objectHydrator(): void;
}
