<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Control;


use Natsu\Model\EntityModel;
use Natsu\Model\Facade\ProgramHighlightFacade;
use Natsu\Model\ProgramModel;

class ProgramHighlightBlockControl extends BaseControl
{
    protected $facade;

    protected $sectionId;

    protected $countDisplayed = 6;

    public function __construct( ProgramHighlightFacade $facade, $sectionId, IContainer $parent = NULL, $name = NULL ) {
        parent::__construct( $parent, $name );

        $this->facade = $facade;
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
        $programs = $this->facade->getRandomizedPrograms(6, $this->sectionId);

        $this->template->highlightPrograms = $programs;

        return $this->template->render();
    }
}