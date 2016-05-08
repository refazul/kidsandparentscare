var BASE_DIR = 'http://localhost/cotfield/';

if (typeof Utility === 'undefined') Utility = (function() {
	var _private = {
		utility_url_parameter_get_by_name: function(name) {
			name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
			var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
				results = regex.exec(location.search);
			return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		},
		utility_object_sort: function(objects, property, order) {
			if (typeof order === 'undefined') order = 'asc';

			function descCompare(a, b) {
				if (parseInt(a[property], 10) < parseInt(b[property], 10))
					return 1;
				if (parseInt(a[property], 10) > parseInt(b[property], 10))
					return -1;
				return 0;
			}

			function ascCompare(a, b) {
				if (parseInt(a[property], 10) < parseInt(b[property], 10))
					return -1;
				if (parseInt(a[property], 10) > parseInt(b[property], 10))
					return 1;
				return 0;
			}
			if (order == 'asc')
				objects.sort(ascCompare);
			else if (order == 'desc')
				objects.sort(descCompare);

			return objects;
		},
		utility_string_number_format: function(x) {
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		utility_id_random_generate: function() {
			var text = "";
			var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
			for (var i = 0; i < 32; i++)
				text += possible.charAt(Math.floor(Math.random() * possible.length));
			return text;
		},
		utility_file_load: function(file, callback) {
			$.get(file, function(data) {
				callback(data);
			});
		},
		utility_tab_new_open: function(link) {
			var win = window.open(link, '_blank');
			win.focus();
		},
		utility_string_ucfirst: function(string) {
			return string.replace(/\w\S*/g, function(txt) {
				return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
			});
		},
		utility_html_template_replace: function(template_id, object, callback) {
			var data = $('#' + template_id).html();
			for (var prop in object) {
				var r = new RegExp('{{' + prop + '}}', "g");
				data = data.replace(r, object[prop]);
			}
			if (typeof callback === 'function')
				callback(data, object);
		}
	};
	return {
		utility_url_parameter_get_by_name: function(name) {
			return _private.utility_url_parameter_get_by_name(name);
		},
		utility_object_sort: function(objects, property, order) {
			return _private.utility_object_sort(objects, property, order);
		},
		utility_string_number_format: function(x) {
			return _private.utility_string_number_format(x)
		},
		utility_id_random_generate: function() {
			return _private.utility_id_random_generate();
		},
		utility_file_load: function(file, callback) {
			_private.utility_file_load(file, callback);
		},
		utility_tab_new_open: function(link) {
			_private.utility_tab_new_open(link);
		},
		utility_string_ucfirst: function(string) {
			return _private.utility_string_ucfirst(string);
		},
		utility_html_template_replace: function(template, object, callback) {
			_private.utility_html_template_replace(template, object, callback);
		}
	};
})();
if (typeof Section === 'undefined') Section = (function() {
	var _private = {
		section_render: function(section_object, parent_selector) {
			if (typeof section_object !== 'object')
				section_object = JSON.parse(section_object);

			// Parse title & fields
			var title = '';
			var field_object = {};
			var fields = [];
			for (var i in section_object) {
				if (i == 'title')
					title = section_object[i];
				else if (i == 'fields')
					field_object = section_object[i];
			}
			for (var field in field_object) {
				fields.push(field_object[field]);
			}

			// Process fields
			for (var i = 0; i < fields.length; i++) {
				var id = fields[i].id;
				var title = fields[i].title;
				var type = fields[i].type;
				var value = fields[i].value;
				var values = fields[i].values ? fields[i].values : {};
				var section_template = $('<div>', {
					id: id + '-wrapper',
					class: 'part'
				});

				$(section_template).append($('<div>', {
					class: 'field'
				}).text(title));
				$(section_template).append($('<div>', {
					class: 'seperator'
				}));
				$(section_template).append($('<div>', {
					class: 'value'
				}).css('width', '280px'));
				$(section_template).append($('<div>', {
					class: 'end'
				}));
				if (type == 'text') {
					$(section_template).find('.value').append($('<input>', {
						type: 'text',
						id: id,
						name: id,
						class: 'form-control text-field text'
					}).attr('autocomplete', 'off')).css('height', '28px');
				} else if (type == 'number') {
					$(section_template).find('.value').append($('<input>', {
						type: 'text',
						id: id,
						name: id,
						class: 'form-control text-field number'
					}).attr('autocomplete', 'off')).css('height', '28px');
				} else if (type == 'select') {
					$(section_template).find('.value').append($('<div>', {
						class: 'select-wrap'
					}));
					$(section_template).find('.value .select-wrap').append($('<select>', {
						id: id,
						name: id
					}).css('height', '100%'));

					for (var v in values) {
						var option = $('<option />').text(values[v]).val(v);
						if (v == value)
							$(option).attr('selected', 'selected');
						$(section_template).find('.value select').append($(option));
					}
				} else if (type == 'date') {
					$(section_template).find('.value').append($('<input>', {
						type: 'text',
						class: 'form-control date'
					}).attr('autocomplete', 'off').attr('data-target', id)).css('height', '28px');
					$(section_template).find('.value').append($('<input>', {
						type: 'hidden',
						id: id,
						name: id
					}));
				} else if (type == 'textbox') {
					$(section_template).find('.value').append($('<textarea>', {
						id: id,
						name: id,
						class: 'form-control text'
					}).attr('autocomplete', 'off').val(value));
				} else if (type == 'file') {
					value = ';general/5709951cd1e09.pdf;general/5709983328bfc.pdf;'
					if (value != '') {
						$(section_template).find('.value').append($('<div>', {
							class: 'files'
						}));

                        // start from here
					}

					Utility.utility_html_template_replace('file_uploader_template', {
						BASE_DIR: BASE_DIR,
						scope: 'general',
						name: Utility.utility_id_random_generate()
					}, function(resultant_template) {
						$(section_template).find('.value').append(resultant_template);
					});

				}
				$(parent_selector).append($(section_template));
			}

			// Date
			$(".date").each(function() {
				var selectedDate = $(this).val();
				if (selectedDate == '') return;

				var date = new moment(selectedDate);
				$(this).val(date.format('Do MMM, YYYY'));
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
				}
			});

			// Text
			$('.text').on('input', function() {
				var new_value = $(this).val().toUpperCase().replace("'", "’");
				var focus = $(this).getCursorPosition();
				$(this).val(new_value);
				$(this).focus();
				$(this).selectRange(focus);
			});

			// Number
			$('.number').on('input', function() {
				var new_value = $(this).val().replace(/[^0-9. ,\-\/]/g, '');
				var focus = $(this).getCursorPosition();
				$(this).val(new_value);
				$(this).focus();
				$(this).selectRange(focus);
			});

			// File
			$(document).ready(function() {

				$('.file').change(function() {
					var form = $(this).closest('form');
					var progress = '.progress';
					var preview = '.preview-image';
					var status = '.status';
					var bar = '.progress-bar';
					var percent = '.percent';

					if (this.files && this.files[0]) {
						var file = this.files[0];
						var name = file.name;
						var size = file.size;
						var type = file.type;
						/* validation */

						$(this).closest('form').ajaxForm({

							/* set data type json */
							dataType: 'json',

							/* reset before submitting */
							beforeSend: function() {
								$(progress, $(form)).fadeIn();
								$(bar, $(form)).width('0%');
								$(percent, $(form)).html('0%');
							},

							/* progress bar call back*/
							uploadProgress: function(event, position, total, percentComplete) {
								var pVel = percentComplete + '%';
								$(bar, $(form)).width(pVel);
								$(percent, $(form)).html(pVel);
								$(status, $(form)).html('Uploading...Please Wait').fadeIn();
							},

							/* complete call back */
							complete: function(data) {
								console.log(data);
								has_image = true;
								$(status, $(form)).html(data.responseJSON.msg).fadeIn();
								if (data.responseJSON.status == 'ok') {
									var _existing_files = $(dest_hook).val().split(';');
									var _new_files = [];
									for (var i = 0; i < _existing_files.length; i++) {
										_new_files.push(_existing_files[i]);
									}
									_new_files.push(data.responseJSON.path);
									if (dest_form == '#') $(dest_hook).val(_new_files.join(';'));

									var container = $('<div>', {
										class: 'docs-container'
									}).attr('data-file', data.responseJSON.path);
									var cross_sign = $('<div>', {
										class: 'cross-sign'
									});
									var docs_icon = $('<div>', {
										class: 'doc-icons'
									});
									var docs_link = $('<a>', {
										class: 'doc-links'
									});

									$(cross_sign).unbind('click');
									$(cross_sign).click(function() {
										var file = $(this).parent().attr('data-file');
										var existing_files = $(dest_hook).val().split(';');
										var new_files = [];
										for (var i = 0; i < existing_files.length; i++) {
											if (file == existing_files[i])
												continue;
											new_files.push(existing_files[i]);
										}
										$(dest_hook).val(new_files.join(';'));
										$(this).parent().remove();
									});

									$(docs_icon).unbind('click');
									$(docs_icon).click(function() {
										var file = '<?php echo site_url();?>uploads/' + $(this).parent().attr('data-file');
										$('body').append('<iframe class="tempviewer" src="http://docs.google.com/gview?url=' + file + '&embedded=true" style="margin:5%;width:90%; height:90%;position:fixed;z-index:100000;" frameborder="0"></iframe>');

										$('.global-overlay').show();
										$('.global-overlay').unbind('click');
										$('.global-overlay').click(function() {
											$(this).hide();
											$('.tempviewer').remove();
										});
									});

									$(docs_link).attr('href', "<?php echo site_url();?>uploads/" + data.responseJSON.path).attr('target', "_blank").text(data.responseJSON.path);

									$(container).append(cross_sign);
									$(container).append(docs_icon);
									$(container).append(docs_link);

									$('#<?php echo $form_id;?> .files').append(container);

									var extension = data.responseJSON.path.split('.').pop();
									$(docs_icon).addClass(extension);
								}

								$(hook).val('');
							}
						});
						$(this).closest('form').submit();
					}
				});
			});
		}
	};
	return {
		section_render: function(section_object, parent_selector) {
			_private.section_render(section_object, parent_selector);
		}
	};
})();

Utility.utility_file_load(BASE_DIR + 'assets/template.json', function(template) {
	$('<div>', {
		id: 'lc'
	}).css('width', '550px').css('clear', 'both').css('margin', '10px auto 20px').appendTo('#module');
	Section.section_render(template.lc, '#lc');
});
