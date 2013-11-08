Sourcero.TreeNodeController = Ember.ObjectController.extend({
	isExpanded: false,

	actions: {
		toggle: function() {
			this.set('isExpanded', !this.get('isExpanded'));
//		},
//
//		click: function() {
//			console.log('Clicked: ' + this.get('obj.name') + ' ' + this.get('path'));
//			this.sendAction()
		}
	}
});
Sourcero.register('controller:treeNode', Sourcero.TreeNodeController, {singleton: false});
