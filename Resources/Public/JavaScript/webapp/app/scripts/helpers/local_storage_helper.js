Sourcero.LocalStorageController = {
	/**
	 * Stores the value for the given key
	 * @param key
	 * @param value
	 */
	set: function(key, value) {
		var prefixedKey = Sourcero.LocalStorageController.buildKeyWithPrefix(key);
		Locstor.set(prefixedKey, value);
	},

	/**
	 * Returns the local storage data for the given key if defined, otherwise the result of the callback
	 * @param key
	 * @param callback
	 */
	get: function(key, callback) {
		var prefixedKey = Sourcero.LocalStorageController.buildKeyWithPrefix(key);
		if (!Locstor.contains(prefixedKey)) {
			if (callback) {
				return callback.call();
			}
			return null;
		}
		return Locstor.get(prefixedKey);
	},

	buildKeyWithPrefix: function(key) {
		return Sourcero.AdapterConfiguration.pkg.name + '.' + key;
	}
};