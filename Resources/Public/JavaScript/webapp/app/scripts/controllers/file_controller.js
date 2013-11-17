Sourcero.FileController = Ember.ObjectController.extend({
	/**
	 * Currently active project
	 */
	pkg: {
		path: '/Applications/MAMP/web_cvjm/typo3conf/ext/bingo'
//		path: 'EXT:sourcero/'
	},

	/**
	 * Currently active file
	 */
	activeFile: null,

	/**
	 * Editor outlet
	 */
	editorOutlet: null,

	/**
	 * Currently opened files
	 */
	openFiles: Ember.A(),



//	/**
//	 * Loads the details of the given file
//	 * @param fileData
//	 */
//	loadFileData: function(fileData) {
//		return;
//		var _this = this,
//			store = _this.get('store'),
//			file = store.find('file', fileData.id),
//			pathUrlComponent = encodeURIComponent(encodeURIComponent(fileData.path)),
//			url;
//
//		url = '/typo3/mod.php?M=tools_SourceroSourcero&'
//			+ 'tx_sourcero_tools_sourcerosourcero%5Baction%5D={{action}}&'.replace(/\{\{action\}\}/, 'file')
//			+ 'tx_sourcero_tools_sourcerosourcero%5Bcontroller%5D=IDE'
//			+ '&tx_sourcero_tools_sourcerosourcero%5Bfile%5D='
//			+ pathUrlComponent;
//
//		console.log(url);
//
//		if (file) {
//			_this.set('activeFile', file);
//		} else {
//			Ember.$.getJSON(url).then(function(data) {
//				var store = _this.get('store'),
//					file = store.createRecord('file', data);
//				_this.set('activeFile', file);
//				console.log(file)
//			});
//		}
//	},

	/**
	 * Returns the editor for the given file or NULL if it doesn't exist
	 */
	editorForFile: function(file) {
		var fileId, editorComponent;

		if (typeof file === 'string') {
			fileId = file;
		} else {
			fileId = file.get('id');
		}

		editorComponent = Ember.View.views[fileId];
		if (editorComponent) {
			return editorComponent;
		}
		return null;
	},


	/**
	 * Returns the editor for the active file
	 */
	editorForActiveFile: function() {
		var activeFile = this.get('model');
		if (!activeFile) return null;
		return this.editorForFile(activeFile);
	},

	/**
	 * Save the active file
	 */
	saveActiveFile: function() {
		var editorComponent = this.editorForActiveFile();
		if (editorComponent) {
			editorComponent.save();
		} else {
			throw "Could not find editor";
		}
	},

	/**
	 * Invoked when the active file changed
	 */
	activeFileChanged: function() {
		var file = this.get('model'),
			fileId;

		if (file) {
			fileId = file.get('id');

			// $('.tab-pane').removeClass('active');
			// $('#' + fileId).addClass('active');

			$('[data-editor]').removeClass('active');
			$('[data-editor="' + fileId + '"]').addClass('active');
		}
	},

	/**
	 * Observes external changes of the edited file
	 */
	activeFileChangedObserver: function() {
		Ember.run.once(this, 'activeFileChanged');
	}.observes('hmodel'),


	init: function() {
		var _this = this;
		this._super();

		Ember.$('body').keydown(function(event) {
			return _this.handleKey(event);
		});
		Sourcero.FileController._instance = this;
	},

	/**
	 * On keydown
	 * @param {Event} event
	 */
	handleKey: function (event) {
		var handled = false;
		if (event.ctrlKey || event.metaKey) {
			if (event.keyCode === 83) { // Save
				this.saveActiveFile();
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

	actions: {
		newTab: function() {
			var store = this.get('store');

			store.push('file', {
				id: 'Classes-Service-SCMService-php',

				name: 'SCMService.php',
				path: '/Classes/Service/SCMService.php',
				directory: '/Classes/Service/',

				type: 'text/x-php',

				size: 2500,
				lastModifiedDate: new Date(1375014198),
				contents: "<?php\n$start=1"
			});
			//this.openFiles.addObject();
		},

		changeTab: function(file) {
			console.log(file);
			this.set('activeFile', file);
//			this.get('editorOutlet').pushObject(editorView);
//			$(window).resize();
//			this.currentView
		},

		fileTreeClick: function(fileNode) {
			var _this = this,
				store = this.get('store');
			store.find('file', fileNode.obj.id).then(function(file) {
				var openFiles = _this.get('openFiles');
				console.log(file)

				_this.set('activeFile', file);
				if (!openFiles.contains(file)) {
					openFiles.pushObject(file);
				}
			});
//			this.loadFileData(fileNode.obj);

		}
	}
});