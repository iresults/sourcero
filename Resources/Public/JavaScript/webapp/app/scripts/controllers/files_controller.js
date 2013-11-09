Sourcero.FilesController = Ember.ObjectController.extend({
	fileTree: null,

	/**
	 * Loads the full file system tree
	 */
	loadFileTree: function () {
		var _this = this,
			pkg = this.get('pkg'),
			pathUrlComponent = encodeURIComponent(encodeURIComponent(pkg.path)),
			url;

		url = '/typo3/mod.php?M=tools_SourceroSourcero&'
			+ 'tx_sourcero_tools_sourcerosourcero%5Baction%5D={{action}}&'.replace(/\{\{action\}\}/, 'fileList')
			+ 'tx_sourcero_tools_sourcerosourcero%5Bcontroller%5D=IDE'
			+ '&tx_sourcero_tools_sourcerosourcero%5Bfile%5D='
			+ pathUrlComponent;

		console.log(url);

		if (Sourcero.FileSystemDummy) {
			this.fileTree = {
				children: Ember.A(Sourcero.FileSystemDummy.fileTree.children)
			};
		}
		Ember.$.getJSON(url).then(function (data) {
			_this.set('fileTree', Ember.Object.create({
				children: Ember.A(data.fileTree.children)
			}));
			_this.propertyDidChange('fileTree.children');
		});
	}
});