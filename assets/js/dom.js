if (typeof Dom === 'undefined') Dom = (function() {
	var _private = {
		dom_report_fetch_form_get: function(){
            return $('#report-fetch');
        }
	};
	return {
        dom_report_fetch_form_get: function(){
            return _private.dom_report_fetch_form_get();
        }
	};
})();
