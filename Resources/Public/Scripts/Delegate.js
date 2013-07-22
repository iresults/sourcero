/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

(function ($) {
    var root = this,
        codeFolding;

    root.console = root.console || {log: function() {}};

    root.delegate = {
        /**
         * Editor instance
         */
        editor: null,

        /**
         * Textarea
         */
        codeTextarea: document.getElementById("code"),

        /**
         * Initialize
         */
        init: function () {
			var _this = this, _editor;

			_editor = this.editor = CodeMirror.fromTextArea(this.codeTextarea, {
                // Add a comment, so this is not treated as Fluid variable
                lineNumbers: true,
                matchBrackets: true,
                theme: "sourcero",
                mode: codeMirrorMode,
                indentUnit: 4,
                indentWithTabs: true,
                enterMode: "keep",
                tabMode: "shift",
                autoCloseTags: true,
                autoCloseBrackets: true,
                lineWrapping: true,
                styleActiveLine: true,
                highlightSelectionMatches: true,
                extraKeys: {
                    "Cmd-.": 			"autocomplete",
                    "Ctrl-.": 			"autocomplete",
                    "Ctrl-/": 			"toggleComment",
					"Cmd-Shift-C": 		"toggleComment",
					"Ctrl-Shift-C": 	"toggleComment",
					"Cmd-Left":			"goLineStartSmart",
					"Cmd-Backspace":	"removeLine",
					"Cmd-D":			"duplicateLine"

					// Captured globally
					// "Cmd-S": 			"saveFile",
					// "Ctrl-S": 			"saveFile",
                    // "Cmd-O": 			"fastOpen",
					// "Ctrl-O":			"fastOpen",
                }
            });
            _editor.on("gutterClick", codeFolding);
			_editor.on("change", _this.markPageTitleAsModified);

            this.restoreCursorPosition();
			this.resizeEditor();
			this.updatePageTitle();

			$('body').keydown(function (event) {
				return _this.captureKeydown(event);
			});
        },

		/**
		 * Resize the editor
		 */
		resizeEditor: function () {
			var _editor = this.editor,
				editorContainerHeight = $('.editor-container').height();
			$(_editor.getWrapperElement()).css('height', editorContainerHeight);
			_editor.refresh();
		},

		/**
		 * On keydown
		 * @param {Event} event
		 */
		captureKeydown: function (event) {
			var handled = false;
			if (event.ctrlKey || event.metaKey) {
				if (event.keyCode === 83) { // Save
					this.saveFile();
					handled = true;
				} else if (event.keyCode === 79) { // Open
					if (root.FastOpen) {
						root.FastOpen.show();
						handled = true;
					}
				}
			}
			return !handled;
		},

		/**
		 * Updates the page title
		 */
		updatePageTitle: function () {
			var title = IDE.extension.extensionKey + ' - ' + IDE.currentFile.name;
			if (this.editor && !this.editor.isClean()) {
				title = '! ' + title;
			}
			root.document.title = title;
		},

        /**
         * Mark the page title as modified
         */
        markPageTitleAsModified: function () {
            var delegateInstance = root.delegate;
            delegateInstance.editor.off("change", delegateInstance.markPageTitleAsModified);
            delegateInstance.updatePageTitle();
        },

        /**
         * Tries to restore the cursor position from the localStorage
         */
        restoreCursorPosition: function () {
            var cursorPosition = root.localStorage.getItem(this.getCursorPositionKeyForLocalStorage()),
                scrollInformation = root.localStorage.getItem(this.getScrollInformationKeyForLocalStorage());

            if (scrollInformation) {
                scrollInformation = JSON.parse(scrollInformation);
                this.editor.scrollTo(0, scrollInformation.top);
            }

            if (cursorPosition) {
                cursorPosition = JSON.parse(cursorPosition);
                this.editor.setCursor(cursorPosition);
            }
        },

        /**
         * Saves the current cursor position
         */
        saveCursorPosition: function () {
            var serializedCursorPosition = JSON.stringify(this.editor.getCursor()),
                serializedScrollInformation = JSON.stringify(this.editor.getScrollInfo());

            root.localStorage.setItem(this.getCursorPositionKeyForLocalStorage(), serializedCursorPosition);
            root.localStorage.setItem(this.getScrollInformationKeyForLocalStorage(), serializedScrollInformation);
        },

        /**
         * Save the file
         */
        saveFile: function () {
            // Save the cursor position
            this.saveCursorPosition();

			this.editor.save();

            // Save the file
			var _this = this,
                form = $(this.codeTextarea.form),
				data = form.serialize(),
				url = form.attr('action');

			this.editor.doc.markClean();

			if (url && data) {
				$.ajax({
					type: "POST",
					url: url,
					data: data,
					dataType: 'text',
					cache: false,
					success: function (data, textStatus, jqXHR) {
						_this.saveSuccess(data, textStatus, jqXHR);
					},
					error: function (jqXHR, textStatus, errorThrown) {
						_this.saveError(jqXHR, textStatus, errorThrown);
					}

				});
			}
        },

        saveSuccess: function (data, textStatus, jqXHR) {
            var jsonData;
			try {
				jsonData = JSON.parse(data);
			} catch (e) {}
            if (jsonData && !jsonData.success) {
                if (jsonData.error && jsonData.error.message) {
                    this.displaySaveError(jsonData.error.message);
                } else {
                    this.displaySaveError();
                }
                return;
            } else if(!jsonData) {
				this.displaySaveError();
				return;
			}
            this.editor.doc.markClean();
            this.editor.on("change", this.markPageTitleAsModified);
            this.updatePageTitle();
            console.log('File successfully saved');
        },

        saveError: function (jqXHR, textStatus, errorThrown) {
			var jsonData;
			try {
				jsonData = JSON.parse(jqXHR.responseText);
			} catch (e) {}
            if (jsonData && jsonData.error && jsonData.error.message) {
                this.displaySaveError(jsonData.error.message);
            } else {
                this.displaySaveError();
            }
            console.log(jqXHR, textStatus, errorThrown);
        },

        /**
         * Display the message to inform the user that the file could not be saved
         * @param message
         */
        displaySaveError: function (message) {
            if (!message) {
                message = 'Could not save file';
            }
            bootbox.alert(message + '', function () {
				root.delegate.editor.focus();
			});
        },

		/**
		 * Duplicates the current line or selection
		 */
		duplicateLine: function () {
			var _editor = this.editor,
				document = _editor.doc,
				selection = document.getSelection();
			if (selection && selection !== '') {
				document.replaceSelection(selection + selection);
			}

		},

		/**
		 * Duplicates the current line or selection
		 */
		removeLine: function () {
			var _editor = this.editor,
				document = _editor.doc,
				cursor = _editor.getCursor();

			if (cursor) {
				document.removeLine(cursor.line);
				_editor.setCursor(cursor);
			}
		},

        /**
         * Returns the key to set or get the cursor position
         */
        getCursorPositionKeyForLocalStorage: function () {
            return this.getKeyPrefixForLocalStorage() + '.editor.cursorPosition';
        },

        /**
         * Returns the key to set or get the scroll information
         */
        getScrollInformationKeyForLocalStorage: function () {
            return this.getKeyPrefixForLocalStorage() + '.editor.scrollInformation';
        },

        /**
         * Returns the key prefix to distinguish the different editors
         */
        getKeyPrefixForLocalStorage: function () {
            var relativeFilePath = IDE.currentFile.path.replace(new RegExp(IDE.extension.extensionPath + '', 'g'), '');
            return (relativeFilePath).replace(/[^a-zA-Z]/g, '.');
        },

        /**
         * Get the completion list
         * @param Object editor Code mirror editor
         */
        getCompletionList: function (editor) {
            var completionObject,
                completionCallback,
                currentPosition = editor.getCursor(),
                token = editor.getTokenAt(currentPosition);

            if (CodeMirror[codeMirrorModeHint]) {
                completionCallback = CodeMirror[codeMirrorModeHint];
                completionObject = completionCallback(editor);
            } else {
                completionObject = {
                    from: CodeMirror.Pos(currentPosition.line, token.start),
                    to: CodeMirror.Pos(currentPosition.line, token.end),
                    list: []
                };
            }

            // Merge the language dependant and general lists
            completionObject.list = jQuery.merge(
                completionObject.list,
                root.delegate.getGeneralCompletionList(editor, token)
            );

            // Remove duplicate entries
            completionObject.list = root.delegate.arrayUnique(completionObject.list);
            return completionObject;
        },

        /**
         * Get the general completion list
         * @param Object editor Code mirror editor
         */
        getGeneralCompletionList: function (editor, token) {
            var contents = this.editor.getValue(),
                contentsArray,
                start = token.string;
            contentsArray = contents.match(/[a-zA-Z0-9\$\-\_@]+/g);
            return contentsArray.filter(function (element, index, array) {
                return element.indexOf(start) === 0 && element !== start;
            });
        },

        /**
         * Removes duplicates from an array
         * @param arr
         */
        arrayUnique: function (arr) {
            // do the default behavior only if we got an array of elements
            if (arr[0] && !!arr[0].nodeType) {
                return $.unique.apply(this, arguments);
            } else {
                // reduce the array to contain no dupes via grep/inArray
                return $.grep(arr, function (v, k) {
                    return $.inArray(v, arr) === k;
                });
            }
        }
    };

    root.delegate.init();

    CodeMirror.commands.autocomplete = function (cm) {
        CodeMirror.showHint(cm, root.delegate.getCompletionList);
    };
    CodeMirror.commands.saveFile = function (cm) {
        root.delegate.saveFile();
    };
    CodeMirror.commands.duplicateLine = function (cm) {
        root.delegate.duplicateLine();
    };
    CodeMirror.commands.removeLine = function (cm) {
        root.delegate.removeLine();
    };
    CodeMirror.commands.fastOpen = function (cm) {
        if (root.FastOpen) {
            root.FastOpen.show();
        }
    };
    codeFolding = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder);

    /**
     * Delete button class
     */
    root.DeleteButton = {
        init: function (selector) {
            var _this = this;
            $(selector).click(function () {
                return _this.click(this);
            });
        },

        click: function (button) {
            var deleteUrl = button.href,
                message = "Really delete file '" + $(button).data('filename') + "'";
            bootbox.confirm(message, function (result) {
                if (result) {
                    window.location = deleteUrl;
                }
            });
            return false;
        }
    };

    /**
     * Save button class
     */
    root.SaveButton = {
        init: function (selector) {
            var _this = this;
            $(selector).click(function () {
                return root.delegate.saveFile();
            });
        }
    };

    $(function () {
        root.deleteButtons = DeleteButton.init('.deleteButton');
        root.saveButtons = SaveButton.init('.saveButton');
    });

})(jQuery);
