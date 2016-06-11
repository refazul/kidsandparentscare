if (typeof Report === 'undefined') Report = (function() {
	var _private = {
		report_build: function(report_type) {
			$.get(BASE + 'assets/template/table.phtml', function(template) {
				$('#module').append(template);

				Dom.dom_report_fetch_form_get().on('submit', function() {
					var endpoint = $(this).attr('data-endpoint');
					var data = {};

					data.limit = $('#limit').val();
					data.page = $('#page').val();
					data.order = $('[name="order"]:checked').val();
					data.sort_by = $('[name="sort_by"]').val();
					data.buyer = $('#buyer').val();
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
							Report.report_update(response, {from:$('#from').val(),to:$('#to').val()}, {sort_by: $('#sort_by').val(), limit: $('#limit').val(), page: $('#page').val(), order: $('[name="order"]:checked').val()});
						}
					});
				});

				$("#slider").slider({
					range: "max",
					min: 1,
					max: 100,
					value: 20,
					change: function(event, ui) {
						$("#limit").val(ui.value);
						$('#page').val(0);
						Report.report_update(false, {from:$('#from').val(),to:$('#to').val()}, {sort_by: $('#sort_by').val(), limit: $('#limit').val(), page: $('#page').val(), order: $('[name="order"]:checked').val()});
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
						Report.report_update(false, {from:$('#from').val(),to:$('#to').val()}, {sort_by: $('#sort_by').val(), limit: $('#limit').val(), page: $('#page').val(), order: $('[name="order"]:checked').val()});
					}
				});

				$('#buyer,#supplier').on('change', function() {
					Dom.dom_report_fetch_form_get().submit();
				});

				$('#sort_by').on('change', function() {
					Report.report_update(false, {from:$('#from').val(),to:$('#to').val()}, {sort_by: $('#sort_by').val(), limit: $('#limit').val(), page: $('#page').val(), order: $('[name="order"]:checked').val()});
				});

				Dom.dom_report_fetch_form_get().submit();
			});
		},
		report_update: function(data, filter, arrangement) {
			this.data = data ? data : this.data;
			// Buyer
			var buyers = this.data.buyers;
			var buyer = this.data.buyer;

			$('#buyer').empty();
			$('#buyer').append(
				$('<option></option>').val(-1).html('-SELECT-')
			);
			for (var i = 0; i < buyers.length; i++) {
				var option = $('<option></option>').val(buyers[i].buyer_id).html(buyers[i].name);
				if (buyers[i].buyer_id == buyer)
					$(option).attr('selected', 'selected');
				$('#buyer').append(option);
			}

			// Supplier
			var suppliers = this.data.suppliers;
			var supplier = this.data.supplier;

			$('#supplier').empty();
			$('#supplier').append(
				$('<option></option>').val(-1).html('-SELECT-')
			);
			for (var i = 0; i < suppliers.length; i++) {
				var option = $('<option></option>').val(suppliers[i].supplier_id).html(suppliers[i].name);
				if (suppliers[i].supplier_id == supplier)
					$(option).attr('selected', 'selected');
				$('#supplier').append(option);
			}

			// Projects
			var projects = Filter.filter_project(this.data.result, filter);
			projects = Sort.sort_project(this.data.result, arrangement);
			projects = Trim.trim_project(this.data.result, arrangement);
			var columns = ["PROJECT ID", "BUYER", "SUPPLIER", "CONTRACT NUMBER", "CONTRACT DATE", "ORIGIN", "PRICE", "PAYMENT", "QTY"];
			$('#result thead').empty();
			$('#result thead').append($('<tr>'));
			for (var i = 0; i < columns.length; i++) {
				var th = $('<th>').text(columns[i]);
				th.appendTo($('#result thead tr'));
			}

			Project.project_template_init(function() {
				$('#result tbody').empty();
				var total_quantity = 0;
				for (var i = 0; i < projects.length; i++) {
					var tr = $('<tr>');
					if (i % 2 == 0)
						tr.addClass('even')
					else
						tr.addClass('odd');
					$('<td>').text(projects[i].name).appendTo(tr);
					$('<td>').text(Buyer.buyer_name_resolve(buyers, projects[i].buyer_id)).appendTo(tr);
					$('<td>').text(Supplier.supplier_name_resolve(suppliers, projects[i].supplier_id)).appendTo(tr);
					$('<td>').text(Project.project_attribute_get(projects[i], 'contract_number')).appendTo(tr);
					$('<td>').text(Project.project_attribute_get(projects[i], 'contract_date')).appendTo(tr);
					$('<td>').text(Project.project_attribute_title_get(projects[i], 's_c_origin')).appendTo(tr);
					$('<td>').text(Project.project_attribute_get(projects[i], 's_c_price') + ' ' + Project.project_attribute_title_get(projects[i], 's_c_price_unit')).appendTo(tr);
					$('<td>').text(Project.project_attribute_title_get(projects[i], 's_c_payment')).appendTo(tr);
					$('<td>').text(Project.project_attribute_get(projects[i], 's_c_quantity') + ' ' + Project.project_attribute_title_get(projects[i], 's_c_quantity_unit')).appendTo(tr);

					tr.appendTo($('#result tbody'));

					var quantity = parseInt(Project.project_attribute_get(projects[i], 's_c_quantity'));
					if (quantity > 0)
						total_quantity += quantity;
				}

				$('#total_quantity').text(total_quantity + ' MT');
			});
		}
	};
	return {
		report_build: function(report_type) {
			_private.report_build(report_type);
		},
		report_update: function(data, filter, arrangement) {
			_private.report_update(data, filter, arrangement);
		}
	};
})();
