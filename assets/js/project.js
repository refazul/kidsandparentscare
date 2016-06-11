if (typeof Project === 'undefined') Project = (function() {
	var _private = {
		project_template_init: function(callback) {
			var that = this;
			$.get('../assets/template.json', function(template) {
				that.template = template;
				if (typeof callback === 'function')
					callback();
			});
		},
		project_attribute_get: function(project, attribute) {
			for (var c in project) {
				var properties = project[c].split('&');
				for (var i = 0; i < properties.length; i++) {
					var key = properties[i].split('=')[0]
					var value = properties[i].split('=')[1];
					if (key == attribute)
						return decodeURIComponent(value);
				}
			}
			return '';
		},
		project_attribute_title_get: function(project, attribute) {
			var that = this;
			for (var c in project) {
				var properties = project[c].split('&');
				for (var i = 0; i < properties.length; i++) {
					var key = properties[i].split('=')[0]
					var value = properties[i].split('=')[1];
					if (key == attribute) {
						var is_unit = false;
						if (attribute.indexOf('_unit') > -1)
							is_unit = true;
						attribute = attribute.split('_unit')[0];
						return Template.template_value_resolve(that.template, attribute, value, is_unit);
					}
				}
			}
			return '';
		}
	};
	return {
		project_template_init: function(callback) {
			_private.project_template_init(callback);
		},
		project_attribute_get: function(project, attribute) {
			return _private.project_attribute_get(project, attribute);
		},
		project_attribute_title_get: function(project, attribute) {
			return _private.project_attribute_title_get(project, attribute);
		}
	};
})();

if (typeof Buyer === 'undefined') Buyer = (function() {
	var _private = {
		buyer_name_resolve: function(buyers, buyer_id) {
			for (var i = 0; i < buyers.length; i++) {
				if (buyers[i].buyer_id == buyer_id)
					return buyers[i].name;
			}
			return '';
		}
	};
	return {
		buyer_name_resolve: function(buyers, buyer_id) {
			return _private.buyer_name_resolve(buyers, buyer_id);
		}
	};
})();

if (typeof Supplier === 'undefined') Supplier = (function() {
	var _private = {
		supplier_name_resolve: function(suppliers, supplier_id) {
			for (var i = 0; i < suppliers.length; i++) {
				if (suppliers[i].supplier_id == supplier_id)
					return suppliers[i].name;
			}
			return '';
		}
	};
	return {
		supplier_name_resolve: function(suppliers, supplier_id) {
			return _private.supplier_name_resolve(suppliers, supplier_id);
		}
	};
})();

if (typeof Template === 'undefined') Template = (function() {
	var _private = {
		template_value_resolve: function(template, attribute, value, is_unit) {
			for (var c in template) {
				var fields = template[c].fields;
				for (var d in fields) {
					if (d == attribute) {
						var values = fields[d].values;
						if (is_unit) {
							values = fields[d].unit.values;
						}
						for (var e in values) {
							if (e == value) {
								return values[e];
							}
						}
					}
				}
			}
			return '';
		}
	};
	return {
		template_value_resolve: function(template, attribute, value, is_unit) {
			return _private.template_value_resolve(template, attribute, value, is_unit);
		}
	};
})();

if (typeof Filter === 'undefined') Filter = (function() {
	var _private = {
		filter_project: function(projects, filter) {
			var filtered_projects = [];
			console.log('--Filtering--', filter);
			for (var i = 0; i < projects.length; i++) {
				var contract_date = Project.project_attribute_get(projects[i], 'contract_date');
				if (filter && filter.from && filter.from.length > 1 && contract_date < filter.from){
					continue;
				}
				if (filter && filter.to && filter.to.length > 1 && contract_date > filter.to)
					continue;
				filtered_projects.push(projects[i]);
			}
			return filtered_projects;
		}
	};
	return {
		filter_project: function(projects, filter) {
			return _private.filter_project(projects, filter);
		}
	};
})();

if (typeof Sort === 'undefined') Sort = (function() {
	var _private = {
		sort_project: function(projects, arrangement) {
			var sorted_projects = projects;
			for (var i = 0; i < projects.length; i++) {

			}
			return sorted_projects;
		}
	};
	return {
		sort_project: function(projects, arrangement) {
			return _private.sort_project(projects, arrangement);
		}
	};
})();

if (typeof Trim === 'undefined') Trim = (function() {
	var _private = {
		trim_project: function(projects, arrangement) {
			var trimmed_projects = projects;
			if (arrangement && ((arrangement.limit && arrangement.limit>0) || arrangement.page)) {
				trimmed_projects = [];
				console.log('--Trimming--', arrangement);
				var page = 0;
				for (var i = 0,j=1; i < projects.length; i++,j++) {
					if (page == arrangement.page)
						trimmed_projects.push(projects[i]);
					if (j % arrangement.limit == 0)
						page++;
					if (page > arrangement.page)
						break;
				}
			}
			return trimmed_projects;
		}
	};
	return {
		trim_project: function(projects, arrangement) {
			return _private.trim_project(projects, arrangement);
		}
	};
})();
