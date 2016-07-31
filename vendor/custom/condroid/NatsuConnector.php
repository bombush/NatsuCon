<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Custom\Condroid;


use Natsu\Model\ProgramModel;

class NatsuConnector implements ICondroidExportable
{
    /**
     * @var ProgramModel
     */
    private $model;

    private $sectionId;

    public function __construct(ProgramModel $model, $sectionId) {
        $this->model = $model;
        $this->sectionId = $sectionId;
    }

    /**
     * @return ProgrammeCondroid[]
     */
    public function fetchForCondroid()
    {
        $programs = $this->model->getProgramsList($this->sectionId);

        $exports = [];

        $helper = new Programme();

        foreach($programs as $program)
        {
            $condroidProgramme = new ProgrammeCondroid();
            $condroidProgramme->pid = $program->id;
            $condroidProgramme->author = $program->author;
            $condroidProgramme->title = $program->contentTitle;

            $condroidProgramme->type = $helper->fromType($program->typeId);

            $condroidProgramme->programLine = $program->programGenre;
            $condroidProgramme->location = $program->roomTitle;
            $condroidProgramme->startTime = new \DateTime($program->timeFrom);
            $condroidProgramme->endTime = new \DateTime($program->timeTo);
            $condroidProgramme->annotation = strip_tags($program->text);

            $exports[] = $condroidProgramme;
        }

        return $exports;
    }
}