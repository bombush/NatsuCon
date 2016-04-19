<?php

/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace \Natsu\Forms\Controls;

class AttachmentImages implements \Nette\Forms\IControl
{
    protected $value;

    /**
     * Sets control's value.
     *
     * @param  mixed
     *
     * @return void
     */
    function setValue( $value ) {
        $this->value = $value;
    }

    /**
     * Returns control's value.
     * @return mixed
     */
    function getValue() {
        return $this->value;
    }

    /**
     * @return void
     */
    function validate() {
        return true;
    }

    /**
     * Returns errors corresponding to control.
     * @return array
     */
    function getErrors() {
        return [];
    }

    /**
     * Is control value excluded from $form->getValues() result?
     * @return bool
     */
    function isOmitted() {
        return TRUE;
    }

    /**
     * Returns translated string.
     *
     * @param  string
     * @param  int      plural count
     *
     * @return string
     */
    function translate( $s, $count = NULL ) {
        return '';
    }
}