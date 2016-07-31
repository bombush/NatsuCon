<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Custom\Condroid;


interface ICondroidExportable
{
    /**
     * @return ProgrammeCondroid[]
     */
    public function fetchForCondroid();
}