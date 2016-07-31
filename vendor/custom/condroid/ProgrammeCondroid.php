<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Custom\Condroid;


class ProgrammeCondroid
{
    public $pid;
    public $author;
    public $title;
    public $type;
    public $programLine;
    public $location;

    /** @var \DateTime */
    public $startTime;
    /** @var \DateTime */
    public $endTime;

    public $annotation;
}