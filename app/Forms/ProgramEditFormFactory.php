<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Forms;


use Natsu\Model\ProgramModel;
use Natsu\Model\EntityModel;
use Natsu\Control\ProgramEditFormControl;
use Nette\DI\Container;

class ProgramEditFormFactory extends \Nette\Object
{
    /**
     * @var ProgramModel
     */
    protected $em;

    protected $programId;

    /**
     * @var Container
     */
    protected $context;

    public function __construct(EntityModel $em, Container $context)
    {
        $this->em = $em;
        $this->context = $context;
    }

    /**
     * @param mixed $sectionId
     *
     * @return $this
     */
    public function setSectionId( $sectionId )
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    public function setProgramId($programId)
    {
        $this->programId = $programId;
    }

    public function create()
    {
        $programModel = $this->em->reflection('Program');

        $sectionId = empty($this->sectionId) ? $this->context->getParameters()[ 'sectionId' ] : $this->sectionId;
        //$from
        $form = new ProgramEditFormControl($sectionId);
        $form->setDefaultTimeFrom( $this->context->getParameters()[ 'programStart' ] );
        $form->setDefaultTimeTo( $this->context->getParameters()[ 'programEnd' ] );
        $form->setProgramId($this->programId);
        $form->setEm($programModel, $this->em);


        return $form;
    }
}