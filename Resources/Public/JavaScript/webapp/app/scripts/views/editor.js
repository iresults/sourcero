Sourcero.Editor = Ember.View.extend({
	attributeBindings: ['file'],
	classNames: ['editor-container'],

	/**
	 * Current file
	 */
	file: null,

	/**
	 * Editor instance
	 */
	editor: null,

	/**
	 * Mode
	 */
	codeMirrorMode: function() {
		return 'php';
	}.property(),

	/**
	 * Mode hint
	 */
	codeMirrorModeHint: function() {
		return this.get('codeMirrorMode') + 'Hint';
	}.property(),

	didInsertElement: function() {
		this._super();
		return ;


		var _this = this,
			codeMirrorMode = this.get('codeMirrorMode'),
			_editor, throttled;

//		_editor = this.editor = CodeMirror.fromTextArea(this.$().get(0), {
		_editor = this.editor = CodeMirror(this.$().get(0), {
			value: this.get('file.contents'),
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
			showTrailingSpace: true,
			extraKeys: {
				"Cmd-.": 			"autocomplete",
				"Ctrl-.": 			"autocomplete",
				"Ctrl-/": 			"toggleComment",
				"Shift-Cmd-C": 		"toggleComment",
				"Shift-Ctrl-C": 	"toggleComment",
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
		// */
//		_editor.on("gutterClick", codeFolding);
		_editor.on("change", function() {
			_this.markPageTitleAsModified();
		});

		this.restoreCursorPosition();
//		this.resizeEditor();
		this.updatePageTitle();

		/* Load the mode */
		CodeMirror.autoLoadMode(_editor, (codeMirrorMode === 'text/x-scss' ? 'css' : codeMirrorMode));


//		$('body').keydown(function (event) {
//			return _this.captureKeydown(event);
//		});


//		Ember.run.throttle(this, function() {
//			_this.resizeEditor();
//		}, 150);
//
//		throttled = jQuery.throttle(0, false, function () {
//			_this.resizeEditor();
//		});
//		$(window).resize(throttled);


//		throttled = (function () {
//			_this.resizeEditor();
//		});
//		$(window).resize(throttled);

		CodeMirror.commands.autocomplete = function (cm) {
			CodeMirror.showHint(cm, function(editor) {
				return _this.getCompletionList(editor);
			});
		};
		CodeMirror.commands.saveFile = function (cm) {
			_this.saveFile();
		};
		CodeMirror.commands.duplicateLine = function (cm) {
			_this.duplicateLine();
		};
		CodeMirror.commands.removeLine = function (cm) {
			_this.removeLine();
		};
		CodeMirror.commands.fastOpen = function (cm) {
			if (window.FastOpen) {
				window.FastOpen.show();
			}
		};
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
				if (window.FastOpen) {
					window.FastOpen.show();
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
		var _controller = this.get('targetObject'),
			title = this.get('file.name');

		if (_controller) {
			title += ' - ' + _controller.get('package.name');
		}
		if (this.editor && !this.editor.isClean()) {
			title = '! ' + title;
		}
		window.document.title = title;
	},

	/**
	 * Mark the page title as modified
	 */
	markPageTitleAsModified: function () {
		var delegateInstance = this;
		delegateInstance.editor.off("change", delegateInstance.markPageTitleAsModified);
		delegateInstance.updatePageTitle();
	},

	/**
	 * Tries to restore the cursor position from the localStorage
	 */
	restoreCursorPosition: function () {
		var cursorPosition = window.localStorage.getItem(this.getCursorPositionKeyForLocalStorage()),
			scrollInformation = window.localStorage.getItem(this.getScrollInformationKeyForLocalStorage());

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

		window.localStorage.setItem(this.getCursorPositionKeyForLocalStorage(), serializedCursorPosition);
		window.localStorage.setItem(this.getScrollInformationKeyForLocalStorage(), serializedScrollInformation);
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
			_this.editor.focus();
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
		return this.get('file.path').replace(/[^a-zA-Z]/g, '.');
	},

	/**
	 * Get the completion list
	 * @param Object editor Code mirror editor
	 */
	getCompletionList: function (editor) {
		var _this = this,
			completionObject,
			completionCallback,
			currentPosition = editor.getCursor(),
			token = editor.getTokenAt(currentPosition);

		if (CodeMirror[this.get('codeMirrorModeHint')]) {
			completionCallback = CodeMirror[this.get('codeMirrorModeHint')];
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
			_this.getGeneralCompletionList(editor, token)
		);

		// Remove duplicate entries
		completionObject.list = _this.arrayUnique(completionObject.list);
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

		contentsArray = contents.match(/[a-zA-Z0-9\$\-\_@]*[a-z0-9_]/g);
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


});