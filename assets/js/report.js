if (typeof Report === 'undefined') Report = (function() {
	var _private = {
		report_build: function(base, report_type) {
			this.base = base;
			$.get(BASE + 'assets/template/table.phtml', function(template) {
				$('#module').append(template);
				if (report_type == 'stockentry') {
					Dom.dom_report_fetch_form_get().attr('data-endpoint', base + 'reports/calc/stockentry');
				}
                else
					return;

				Dom.dom_report_fetch_form_get().on('submit', function() {
					var endpoint = $(this).attr('data-endpoint');
					var data = {};

					data.limit = $('#limit').val();
					data.page = $('#page').val();
					data.order = $('[name="order"]:checked').val();
					data.sort_by = $('[name="sort_by"]').val();
					data.department = $('#department').val();
					data.supplier = $('#supplier').val();
					data.from = $('#from').val();
					data.to = $('#to').val();

					$.ajax({
						url: endpoint,
						method: 'POST',
						data: data,
						success: function(response) {
							if (typeof response !== 'object')
								response = JSON.parse(response);
							Report.report_update(response, {
								from: $('#from').val(),
								to: $('#to').val()
							}, {
								sort_by: $('#sort_by').val(),
								limit: $('#limit').val(),
								page: $('#page').val(),
								order: $('[name="order"]:checked').val()
							});
						}
					});
				});

				$("#slider").slider({
					range: "max",
					min: 1,
					max: 1000,
					value: 200,
					change: function(event, ui) {
						$("#limit").val(ui.value);
						$('#page').val(0);
						Report.report_update(false, {
							from: $('#from').val(),
							to: $('#to').val()
						}, {
							sort_by: $('#sort_by').val(),
							limit: $('#limit').val(),
							page: $('#page').val(),
							order: $('[name="order"]:checked').val()
						});
					},
					slide: function(event, ui) {
						$('#limit-view').html(ui.value);
					}
				});

				$(".date").not('.disabled').datepicker({
					dateFormat: 'yy-mm-dd',
					defaultDate: "+0w",
					changeMonth: true,
					numberOfMonths: 1,
					onSelect: function(selectedDate) {
						$('#' + $(this).attr('data-target')).val(selectedDate);
						var date = new moment(selectedDate);
						$(this).val(date.format('Do MMM, YYYY'));
						Report.report_update(false, {
							from: $('#from').val(),
							to: $('#to').val()
						}, {
							sort_by: $('#sort_by').val(),
							limit: $('#limit').val(),
							page: $('#page').val(),
							order: $('[name="order"]:checked').val()
						});
					}
				});

				$('#department,#supplier').on('change', function() {
					Dom.dom_report_fetch_form_get().submit();
				});

				$('#sort_by').on('change', function() {
					Report.report_update(false, {
						from: $('#from').val(),
						to: $('#to').val()
					}, {
						sort_by: $('#sort_by').val(),
						limit: $('#limit').val(),
						page: $('#page').val(),
						order: $('[name="order"]:checked').val()
					});
				});

				Dom.dom_report_fetch_form_get().submit();
			});
		},
		report_update: function(data, filter, arrangement) {
			var that = this;
			this.data = data ? data : this.data;
			// Columns
			var columns = this.data.columns;
			$('#result thead').empty();
			$('#result thead').append($('<tr>'));
			for (var i = 0; i < columns.length; i++) {
				var th = $('<th>').text(columns[i].title);
				th.appendTo($('#result thead tr'));
			}

			// Sort By
			var sort_by = $('#sort_by').val();
			$('#sort_by').empty();
			for (var i = 0; i < columns.length; i++) {
				var id = columns[i].fields.active ? columns[i].fields.active : (columns[i].fields.passive ? columns[i].fields.passive : columns[i].fields.extractable);
				if (!id) continue;
				var option = $('<option></option>').val(id).html(columns[i].title);
				if (id == sort_by)
					$(option).attr('selected', 'selected');
				$('#sort_by').append(option);
			}

			// Supplier
			var suppliers = this.data.suppliers;
			var supplier = this.data.supplier;

			$('#supplier').empty();
			$('#supplier').append(
				$('<option></option>').val(-1).html('-SELECT-')
			);
			for (var i = 0; i < suppliers.length; i++) {
				var option = $('<option></option>').val(suppliers[i].sid).html(suppliers[i].name);
				if (suppliers[i].sid == supplier)
					$(option).attr('selected', 'selected');
				$('#supplier').append(option);
			}

            // Department
			var departments = this.data.departments;
			var department = this.data.department;

			$('#department').empty();
			$('#department').append(
				$('<option></option>').val(-1).html('-SELECT-')
			);
			for (var i = 0; i < departments.length; i++) {
				var option = $('<option></option>').val(departments[i].did).html(departments[i].name);
				if (departments[i].did == department)
					$(option).attr('selected', 'selected');
				$('#department').append(option);
			}

			// Results
			var results = Filter.filter_result(this.data.result, filter);
			results = Sort.sort_result(results, arrangement);
			results = Trim.trim_result(results, arrangement);


			$('#result tbody').empty();
			var total_cost = 0;
			for (var i = 0; i < results.length; i++) {
				var tr = $('<tr>');
				if (i % 2 == 0)
					tr.addClass('even')
				else
					tr.addClass('odd');
				$('<td>').text(results[i].stid).appendTo(tr);
				$('<td>').text(results[i].sku).appendTo(tr);
				$('<td>').text(Supplier.supplier_name_resolve(suppliers, results[i].sid)).appendTo(tr);
                $('<td>').text(Department.department_name_resolve(departments, results[i].did)).appendTo(tr);
                $('<td>').text(results[i].unit_cost).appendTo(tr);
                $('<td>').text(results[i].quantity).appendTo(tr);
                $('<td>').text(results[i].unit_cost * results[i].quantity).appendTo(tr);
                $('<td>').text(results[i].stocked_on).appendTo(tr);

				tr.appendTo($('#result tbody'));

				tr.attr('data-stock', results[i].stid);

				var cost = parseFloat(parseFloat(results[i].unit_cost) * parseFloat(results[i].quantity)).toFixed(2);
				if (cost > 0)
					total_cost += cost;
			}

			$('tr').click(function(e) {
				if ($(this).attr('data-stock'))
					window.open(that.base + 'stocks/edit/' + $(this).attr('data-stock'));
			});

			$('#total_cost').text(total_cost);
		}
	};
	return {
		report_build: function(base, report_type) {
			_private.report_build(base, report_type);
		},
		report_update: function(data, filter, arrangement) {
			_private.report_update(data, filter, arrangement);
		}
	};
})();

if (typeof Supplier === 'undefined') Supplier = (function() {
	var _private = {
		supplier_name_resolve: function(suppliers, supplier_id) {
			for (var i = 0; i < suppliers.length; i++) {
				if (suppliers[i].sid == supplier_id)
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

if (typeof Department === 'undefined') Department = (function() {
	var _private = {
		department_name_resolve: function(departments, department_id) {
			for (var i = 0; i < departments.length; i++) {
				if (departments[i].did == department_id)
					return departments[i].name;
			}
			return '';
		}
	};
	return {
		department_name_resolve: function(departments, department_id) {
			return _private.department_name_resolve(departments, department_id);
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
		filter_result: function(results, filter) {
			var filtered_results = [];
			console.log('--Filtering--', filter);
			for (var i = 0; i < results.length; i++) {
				var stocked_on = results[i].stocked_on;
				if (filter && filter.from && filter.from.length > 1 && stocked_on < filter.from) {
					continue;
				}
				if (filter && filter.to && filter.to.length > 1 && stocked_on > filter.to)
					continue;
				filtered_results.push(results[i]);
			}
			return filtered_results;
		}
	};
	return {
		filter_result: function(results, filter) {
			return _private.filter_result(results, filter);
		}
	};
})();

if (typeof Sort === 'undefined') Sort = (function() {
	var _private = {
		sort_result: function(results, arrangement) {
			var sorted_results = results;
			for (var i = 0; i < results.length; i++) {

			}
			return sorted_results;
		}
	};
	return {
		sort_result: function(results, arrangement) {
			return _private.sort_result(results, arrangement);
		}
	};
})();

if (typeof Trim === 'undefined') Trim = (function() {
	var _private = {
		trim_result: function(results, arrangement) {
			var trimmed_results = results;
			if (arrangement && ((arrangement.limit && arrangement.limit > 0) || arrangement.page)) {
				trimmed_results = [];
				console.log('--Trimming--', arrangement);
				var page = 0;
				for (var i = 0, j = 1; i < results.length; i++, j++) {
					if (page == arrangement.page)
						trimmed_results.push(results[i]);
					if (j % arrangement.limit == 0)
						page++;
					if (page > arrangement.page)
						break;
				}
			}
			return trimmed_results;
		}
	};
	return {
		trim_result: function(results, arrangement) {
			return _private.trim_result(results, arrangement);
		}
	};
})();
