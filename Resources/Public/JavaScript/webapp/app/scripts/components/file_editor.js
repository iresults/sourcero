Sourcero.FileEditorComponent = Ember.Component.extend({
	attributeBindings: ['file'],
	classNames: ['editor-container', 'tab-pane', 'active'],

	/**
	 * Current file
	 */
	file: null,

	/**
	 * Editor instance
	 * {CodeMirror}
	 */
	editor: null,

	/**
	 * Collection of CodeMirror documents
	 */
	documents: Ember.A(),

	/**
	 * Defines if the editor was initialized
	 */
	editorIsInitialized: false,

	/**
	 * Returns the editor settings
	 */
	editorSettings: function() {
		return {
			lineNumbers: true,
			matchBrackets: true,
			theme: "sourcero",
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
		};
	}.property(),

	/**
	 * Mode
	 */
	codeMirrorMode: function() {
		return this.getCodeMirrorModeForFile(this.get('file'));
	}.property(),

	/**
	 * Mode hint
	 */
	codeMirrorModeHint: function() {
		return this.get('codeMirrorMode') + 'Hint';
	}.property(),

	/**
	 * The attached file's content
	 */
	fileContents: function() {
		var contents = this.get('file.contents');
		return contents ? contents : '';
	}.property('file'),



	// INITIALIZE
	init: function() {
		var fileId;
		this._super();

		fileId = this.get('file.id');
		console.log('fileEditor init', fileId)
		if (fileId) {
			this.set('elementId', fileId);
		}
	},

	/**
	 * Initialize the CodeMirror instance
	 */
	initializeCodeMirror: function() {
		var _this = this,
			codeMirrorMode = this.get('codeMirrorMode'),
			contents = this.get('fileContents'),
			_editor, throttled;

		if (this.editorIsInitialized) {
			return;
		}
		this.editorIsInitialized = true;


//		_editor = this.editor = CodeMirror.fromTextArea(this.$().get(0), {
		_editor = this.editor = CodeMirror(
			this.$().get(0),
			Ember.merge(this.get('editorSettings'), {
				value: contents,
				mode: codeMirrorMode
			})
		);
		// */
//		_editor.on("gutterClick", codeFolding);
		_editor.on("change", function() {
			_this.fileContentsChanged();
		});

//		this.restoreCursorPosition();
//		this.resizeEditor();

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
		CodeMirror.commands.duplicateLine = function (cm) {
			_this.duplicateLine();
		};
		CodeMirror.commands.removeLine = function (cm) {
			_this.removeLine();
		};
	},

	/**
	 * Returns the name of the CodeMirror mode for the given file
	 * @param  {Sourcero.File} file
	 * @return string
	 */
	getCodeMirrorModeForFile: function(file) {
		mimeType = file.get('type');
		mode = mimeType.split(';', 1)[0]
			.replace(/application\/x-/, '')
			.replace(/text\/x-/, 		'')
			.replace(/application\//, 	'')
			.replace(/text\//, 			'')
		;

		if (mode === 'html') {
			mode = 'htmlmixed';
		} else if (mode === 'scss') {
			mode = 'text/x-scss';
		}
		return mode;
	},

//	/**
//	 * Handles external changes of the edited file or the full change of the edited file
//	 */
//	fileChangedExternal: function() {
//		var _editor = this.editor,
//			contents = this.get('targetObject.model.contents');
//		if (contents) {
//			_editor.setValue(contents);
//			_editor.clearHistory();
//			_editor.markPageTitleAsUnmodified();
//		}
//	},
//
//	/**
//	 * Observes external changes of the edited file
//	 */
//	fileChangedExternalObserver: function() {
//		Ember.run.once(this, 'fileChangedExternal');
//	}.observes('targetObject.file'),

	/**
	 * Invoked when the file content changed
	 */
	fileContentsChanged: function() {

	},

	/**
	 * Invoked when the active file changed
	 */
	fileChanged: function() {
		var file = this.get('file'),
			contents = file.get('contents'),
			mode = this.getCodeMirrorModeForFile(file),
			cmDocument = CodeMirror.Doc(contents, mode);

		console.log('Swap doc', cmDocument);
		this.editor.swapDoc(cmDocument);
		this.refresh();
		this.restoreCursorPosition();
	},

	/**
	 * Observes external changes of the edited file
	 */
	fileChangedObserver: function() {
		console.log('Changed file')
		Ember.run.once(this, 'fileChanged');
	}.observes('file'),

	/**
	 * Initialize the editor as soon as the view is inserted
	 */
	didInsertElement: function() {
		this._super();
		this.initializeCodeMirror();

		console.log('didInsertElement fileEditor')
//		this.fileChangedExternal();

	},

	/**
	 * Refresh the editor
	 */
	refresh: function() {
		console.log('Refresh component ' + this);
		this.editor.refresh();
	},


	// PAGE TITLE


	// EDITOR GUI
	/**
	 * Resize the editor
	 */
	resizeEditor: function () {
		var _editor = this.editor,
			editorContainerHeight = $('.editor-container').height();
		$(_editor.getWrapperElement()).css('height', editorContainerHeight);
		_editor.refresh();
	},


	// SETTINGS
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
	save: function () {
		var _this = this,
			file = this.get('file'),
//			clean = this.editor.doc.isClean(),
			contents = this.editor.doc.getValue();

		// Save the cursor position
		this.saveCursorPosition();

		file.set('contents', contents);
		file.save().then(function() {
			_this.editor.doc.markClean();
		});
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


	// EDITOR ADDITIONS
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
	 * Get the completion list
	 * @param {Object} editor Code mirror editor
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
	 * @param {Object} editor Code mirror editor
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
