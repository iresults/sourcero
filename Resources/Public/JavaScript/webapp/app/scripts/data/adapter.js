Sourcero.DataAdapter = DS.Adapter.extend({
	namespace: 'sourcero',

	/**
	 Called by the store in order to fetch the JSON for a given
	 type and ID.

	 The `find` method makes an Ajax request to a URL computed by `buildURL`, and returns a
	 promise for the resulting payload.

	 This method performs an HTTP `GET` request with the id provided as part of the querystring.

	 @method find
	 @see RESTAdapter/buildURL
	 @see RESTAdapter/ajax
	 @param {DS.Store} store
	 @param {subclass of DS.Model} type
	 @param {String} id
	 @returns Promise
	 */
	find: function(store, type, id) {
		return this.ajax(this.buildURL(type.typeKey, id), 'GET');
	},

	/**
	 Called by the store in order to fetch a JSON array for all
	 of the records for a given type.

	 The `findAll` method makes an Ajax (HTTP GET) request to a URL computed by `buildURL`, and returns a
	 promise for the resulting payload.

	 @method findAll
	 @see RESTAdapter/buildURL
	 @see RESTAdapter/ajax
	 @param {DS.Store} store
	 @param {subclass of DS.Model} type
	 @param {String} sinceToken
	 @returns Promise
	 */
	findAll: function(store, type, sinceToken) {
		return this.ajax(this.buildURL(type.typeKey), 'GET');
		var query;

		if (sinceToken) {
			query = { since: sinceToken };
		}

		return this.ajax(this.buildURL(type.typeKey), 'GET', { data: query });
	},

	/**
	 Called by the store when an existing record is saved
	 via the `save` method on a model record instance.

	 The `updateRecord` method serializes the record and makes an Ajax (HTTP PUT) request
	 to a URL computed by `buildURL`.

	 See `serialize` for information on how to customize the serialized form
	 of a record.

	 @method updateRecord
	 @see RESTAdapter/buildURL
	 @see RESTAdapter/ajax
	 @see RESTAdapter/serialize
	 @param {DS.Store} store
	 @param {subclass of DS.Model} type
	 @param {DS.Model} record
	 @returns Promise
	 */
	updateRecord: function(store, type, record) {
		var data = {
				tx_sourcero_tools_sourcerosourcero: {
					path: record.get('id'),
					contents: record.get('contents')
				}
			},
			url = this.urlForAction('update') + "&tx_sourcero_tools_sourcerosourcero%5Bformat%5D=json";

		console.log('updateRecord', url, data);

		return this.ajax(url, 'POST', {data: data});



//
//
//		var data = {},
//			serializer = store.serializerFor(type.typeKey),
//			url = "mod.php?M=tools_SourceroSourcero&tx_sourcero_tools_sourcerosourcero%5Baction%5D=update&"
//				+ "tx_sourcero_tools_sourcerosourcero%5Bcontroller%5D=IDE&"
//				+ "tx_sourcero_tools_sourcerosourcero%5Bformat%5D=json";
//
//		serializer.serializeIntoHash(data, type, record);
//
//		var id = get(record, 'id');
//
//		return this.ajax(url, 'POST', {data: data});
//
//		return this.ajax(this.buildURL(type.typeKey, id), "PUT", { data: data });
	},

	createRecord: function(store, type, record) {},
	deleteRecord: function(store, type, record) {},

	/**
	 Builds a URL for a given type and optional ID.

	 By default, it pluralizes the type's name (for example,
	 'post' becomes 'posts' and 'person' becomes 'people').

	 If an ID is specified, it adds the ID to the path generated
	 for the type, separated by a `/`.

	 @method buildURL
	 @param {String} type
	 @param {String} id
	 @returns String
	 */
	buildURL: function(type, id) {
		var host = this.get('host'),
			pkg = this.get('pkg'),
			action = id ? 'file' : 'fileList',
			pathUrlComponent = '',
			path, url;

		if (id) {
			path = id;
			path = path.replace(/\-_\-/g, '/').replace(/_\-_/g, '.').replace(/_\-\-/g, ':');
			pathUrlComponent = encodeURIComponent(encodeURIComponent(path));
		} else {
			pathUrlComponent = encodeURIComponent(encodeURIComponent(pkg.path));
		}
		return this.urlForAction(action) + '&tx_sourcero_tools_sourcerosourcero%5Bfile%5D=' + pathUrlComponent;
		return url;


		if (type) { url.push(this.pathForType(type)); }
		if (id) { url.push(id); }

		if (prefix) { url.unshift(prefix); }

		url = url.join('/');
		if (!host && url) { url = '/' + url; }

		return url;
	},

	urlForAction: function(action) {
		return '/typo3/mod.php?M=tools_SourceroSourcero&'
			+ 'tx_sourcero_tools_sourcerosourcero%5Baction%5D={{action}}&'.replace(/\{\{action\}\}/, action)
			+ 'tx_sourcero_tools_sourcerosourcero%5Bcontroller%5D=IDE';
	},

	/**
	 Takes a URL, an HTTP method and a hash of data, and makes an
	 HTTP request.

	 When the server responds with a payload, Ember Data will call into `extractSingle`
	 or `extractArray` (depending on whether the original query was for one record or
	 many records).

	 By default, `ajax` method has the following behavior:

	 * It sets the response `dataType` to `"json"`
	 * If the HTTP method is not `"GET"`, it sets the `Content-Type` to be
	 `application/json; charset=utf-8`
	 * If the HTTP method is not `"GET"`, it stringifies the data passed in. The
	 data is the serialized record in the case of a save.
	 * Registers success and failure handlers.

	 @method ajax
	 @private
	 @param  url
	 @param  type
	 @param  hash
	 */
	ajax: function(url, type, hash) {
		var adapter = this;

		return new Ember.RSVP.Promise(function(resolve, reject) {
			hash = adapter.ajaxOptions(url, type, hash);

			hash.success = function(json) {
				Ember.run(null, resolve, json);
			};

			hash.error = function(jqXHR, textStatus, errorThrown) {
				Ember.run(null, reject, adapter.ajaxError(jqXHR));
			};

			Ember.$.ajax(hash);
		});
	},

	ajaxOptions: function(url, type, hash) {
		hash = hash || {};
		hash.url = url;
		hash.type = type;
		hash.dataType = 'json';
		hash.context = this;

//		if (hash.data && type !== 'GET') {
//			hash.contentType = 'application/json; charset=utf-8';
//			hash.data = JSON.stringify(hash.data);
//		}

		if (this.headers !== undefined) {
			var headers = this.headers;
			hash.beforeSend = function (xhr) {
				forEach.call(Ember.keys(headers), function(key) {
					xhr.setRequestHeader(key, headers[key]);
				});
			};
		}


		return hash;
	},

	/**
	 Takes an ajax response, and returns a relavant error.

	 By default, the `ajaxError` method has the following behavior:

	 * It simply returns the ajax response (jqXHR).

	 @method ajaxError
	 @param  jqXHR
	 */
	ajaxError: function(jqXHR) {
		if (jqXHR) {
			jqXHR.then = null;
		}
		return jqXHR;
	}
});