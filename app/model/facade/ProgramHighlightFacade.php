<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Model\Facade;


use Natsu\Model\ProgramModel;
use Natsu\Model\EntityModel;

class ProgramHighlightFacade extends Facade
{
    protected $programModel;

    public function __construct( EntityModel $model, ProgramModel $programModel ) {
        parent::__construct( $model );

        $this->programModel = $programModel;
    }

    /**
     * Get random programs with al least one program of each required type
     *
     * Get all programs with types, fill result array with required types
     * and add (or pop off) elements to get the desired $count
     *
     * Skip if no image!
     *
     * @param $count desired number of randomized programs
     *
     * @return array
     */
    public function getRandomizedPrograms($count, $sectionId, $notIn = null)
    {
        $requiredTypes = [1,2,3,4]; // Prednaska, soutez, promitani, workshop

        $programsWithTypes = $this->programModel->getRandomProgramsList(null, $sectionId);

        $resultRequired = [];
        $resultRest = [];
        foreach($requiredTypes as $type)
            $resultRequired[$type] = [];


        foreach($programsWithTypes as $program) {
            if(!$program->image || (is_array($notIn) && in_array($program->id, $notIn))) //skip if no image or notin
                continue;

            if(count($resultRequired[$program->typeId]) == 0 && in_array( $program->typeId, $requiredTypes ))
                $resultRequired[$program->typeId][] = $program;

            else
                $resultRest[] = $program;
        }


        $result = [];
        foreach($resultRequired as $key => $value) {
            if(count($value) > 0)
                foreach($value as $program)
                    $result[] = $program;
        }


        if(count($result) > $count) {
            while ( count( $result ) > $count )
                array_pop( $result );

        } else if(count($result) < $count) {
            while ( count( $result ) < $count && count( $resultRest ) )
                $result[] = array_shift( $resultRest );
        }


        shuffle($result);

        return $result;
    }
}