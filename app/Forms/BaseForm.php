<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Forms;

use RadekDostal\NetteComponents\DateTimePicker\DateTimePicker;
use RadekDostal\NetteComponents\DateTimePicker\DatePicker;

class BaseForm extends \Nette\Application\UI\Form {

	public function addDatePicker($name, $label = NULL, $maxLength = NULL) {
		$element = $this[$name] = new DatePicker($label, $maxLength);
		return $element;
	}

	public function addDateTimePicker($name, $label = NULL, $maxLength = NULL){
		$element = $this[$name] = new DateTimePicker($label, $maxLength);
		return $element;
	}
} 