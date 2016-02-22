if (typeof Pattern === 'undefined') Pattern = (function() {
	var _private = {
		pattern_api_delay: function(delta, object, event, init_callback, event_callback, post_callback) {
			var that = this;

			that.timeout = false;
			that.delta = delta;

			if (typeof init_callback === 'function')
				init_callback();

			$(object).unbind(event);
			$(object).on(event, function() {
				if (typeof event_callback === 'function')
					event_callback();

				that.rtime = new Date();
				if (that.timeout === false) {
					that.timeout = true;
					setTimeout(pattern_api_delay_complete, that.delta);
				}
			});

			function pattern_api_delay_complete() {
				if (new Date() - that.rtime < that.delta) {
					setTimeout(pattern_api_delay_complete, that.delta);
				} else {
					that.timeout = false;
					if (typeof post_callback === 'function')
						post_callback();
				}
			}
		}
	};
	return {
		pattern_api_delay: function(delta, object, event, init_callback, event_callback, post_callback) {
			return _private.pattern_api_delay(delta, object, event, init_callback, event_callback, post_callback);
		}
	};
})();
