<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Control;


use Natsu\Model\EntityModel;
use Natsu\Model\ProgramModel;

class ProgramHighlightBlockControl extends BaseControl
{
    protected $programModel;

    protected $sectionId;

    protected $countDisplayed = 6;

    public function __construct(ProgramModel $programModel, $sectionId, IContainer $parent = NULL, $name = NULL ) {
        parent::__construct( $parent, $name );

        $this->programModel = $programModel;
        $this->sectionId = $sectionId;
    }

    public function setCountDisplayed( $number) {
        if(!is_int($number))
            throw new \InvalidArgumentException('Integer argument required. Passed in: ' . $number);

        $this->countDisplayed = $number;
    }

    public function render()
    {
        $this->template->setFile( __DIR__ . '/templates/ProgramHighlightBlockControl.latte');
        $programs = $this->programModel->getRandomProgramsList( $this->countDisplayed, $this->sectionId );

        $this->template->highlightPrograms = $programs;

        return $this->template->render();
    }
}