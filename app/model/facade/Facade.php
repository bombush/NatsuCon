<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Model\Facade;


use Natsu\Model\EntityModel;

class Facade
{
    protected $model;

    public function __construct(EntityModel $model)
    {
        $this->model = $model;
    }
}