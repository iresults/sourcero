Sourcero.LocalStorageHelper = {
	/**
	 * Stores the value for the given key
	 * @param {String} key
	 * @param {*} value
	 */
	set: function(key, value) {
		var prefixedKey = Sourcero.LocalStorageHelper.buildKeyWithPrefix(key);
		Locstor.set(prefixedKey, value);
	},

	/**
	 * Returns the local storage data for the given key if defined, otherwise the result of the callback
	 * @param {String} key
	 * @param [callback]
	 */
	get: function(key, callback) {
		var prefixedKey = Sourcero.LocalStorageHelper.buildKeyWithPrefix(key);
		if (!Locstor.contains(prefixedKey)) {
			if (callback) {
				return callback.call();
			}
			return null;
		}
		return Locstor.get(prefixedKey);
	},

	/**
	 * Returns the prefixed key
	 *
	 * @param {String} key
	 * @returns {string}
	 */
	buildKeyWithPrefix: function(key) {
		return Sourcero.AdapterConfiguration.pkg.name + '.' + key;
	}
};
