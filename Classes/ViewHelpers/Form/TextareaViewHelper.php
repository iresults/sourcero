<?php
/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
/**
 * Textarea view helper.
 * The value of the text area needs to be set via the "value" attribute, as with all other form ViewHelpers.
 *
 * = Examples =
 *
 * <code title="Example">
 * <f:form.textarea name="myTextArea" value="This is shown inside the textarea" />
 * </code>
 * <output>
 * <textarea name="myTextArea">This is shown inside the textarea</textarea>
 * </output>
 *
 * @api
 */
class Tx_Sourcero_ViewHelpers_Form_TextareaViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\TextareaViewHelper {
	/**
	 * Renders the textarea.
	 *
	 * @return string
	 * @api
	 */
	public function render() {
		$name = $this->getName();
		$this->registerFieldNameForFormTokenGeneration($name);
		$this->tag->forceClosingTag(TRUE);
		$this->tag->addAttribute('name', $name);

		// Don't escape the value
		// $this->tag->setContent(htmlspecialchars($this->getValue()));
		$this->tag->setContent($this->getValue());

		$this->setErrorClassAttribute();
		return $this->tag->render();
	}
}

?>