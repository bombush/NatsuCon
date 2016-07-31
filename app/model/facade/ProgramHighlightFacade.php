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

        $programsWithTypes = $this->getRandomProgramsListWithImages( null, $sectionId);

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

    /**
     * Get random list of stickied programs with images
     *
     * @param null $count
     * @param      $sectionId
     *
     * @return mixed
     */
    public function getRandomProgramsListWithImages( $count = NULL, $sectionId )
    {
        if ( !is_int( $count ) && !is_null( $count ) )
            throw new \InvalidArgumentException( 'Integer argument required. Passed in: ' . $count );

        $stm = $this->programModel->getProgramsListFluent();

        $stm->orderBy( 'RAND()' );
        $stm->where( 'attachment.id IS NOT NULL' );
        $stm->where( 'content.isSticky = 1' );

        if ( $count )
            $stm->limit( $count );

        if ( isset( $sectionId ) ) {
            $stm->where( "program.sectionId = $sectionId" );
        }

        $programs = $this->programModel->injectImagesToRows( $stm->fetchAll() );

        return $programs;
    }
}