/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * this JS code does the drag+drop logic for the Layout module (Web => Page)
 * based on jQuery UI
 */
define(['jquery', 'jquery-ui/droppable', 'TYPO3/CMS/Backend/LayoutModule/DragDrop'], function ($, Droppable, DragDrop) {
	'use strict';

	/**
	 * initializes Drag+Drop for all content elements on the page
	 */
	DragDrop.initialize = function () {
		$(DragDrop.draggableIdentifier).draggable({
			handle: this.dragHeaderIdentifier,
			scope: 'tt_content',
			cursor: 'move',
			distance: 20,
			addClasses: 'active-drag',
			revert: 'invalid',
			start: function (evt, ui) {
				DragDrop.onDragStart($(this));
			},
			stop: function (evt, ui) {
				DragDrop.onDragStop($(this));
			}
		});
	};

	/**
	 * called when a draggable is selected to be moved
	 * @param $element a jQuery object for the draggable
	 * @private
	 */
	DragDrop.onDragStart = function ($element) {
		// Add css class for the drag shadow
		DragDrop.originalStyles = $element.get(0).style.cssText;
		$element.children(DragDrop.dragIdentifier).addClass('dragitem-shadow');
		$element.append('<div class="ui-draggable-copy-message">' + TYPO3.lang['dragdrop.copy.message'] + '</div>');

		//get dragged element ctype
		var $elementCType = $element.find('.t3-ctype-identifier').data('ctype');

		// Hide create new element button
		$element.children(DragDrop.dropZoneIdentifier).addClass('drag-start');
		$element.closest(DragDrop.columnIdentifier).removeClass('active');

		// make the drop zones visible
		$(DragDrop.dropZoneIdentifier).each(function () {

			var $colPos = $(this).parent().parent().data('colpos');
			var $allowedCtypes = $(this).parent().data('allowed') || $(this).parent().parent().find('[data-allowed]').data('allowed');

			if ($(this).parent().find('.icon-actions-document-new').length) {

				//if ctype is found in allowed ctype string
				//allow drop area
				if($allowedCtypes === '*' || $allowedCtypes.indexOf($elementCType) !== -1) {

					// hide new content buttons in allowed columns
					$element.parents(DragDrop.columnHolderIdentifier).find('[id*="colpos-' + $colPos + '-page"]').hide()
					$element.find($(this)).hide();

					$(this).addClass(DragDrop.validDropZoneClass);
				}

			} else {
				$(this).closest(DragDrop.contentIdentifier).find('> ' + DragDrop.addContentIdentifier + ', > > ' + DragDrop.addContentIdentifier).show();
			}
		});
	};

	$(DragDrop.initialize);
	return DragDrop;
});
