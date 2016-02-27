$.fn.selectRange = function(start, end) {
   if(typeof end === 'undefined') {
      end = start;
   }
   return this.each(function() {
      if('selectionStart' in this) {
         this.selectionStart = start;
         this.selectionEnd = end;
      } else if(this.setSelectionRange) {
         this.setSelectionRange(start, end);
      } else if(this.createTextRange) {
         var range = this.createTextRange();
         range.collapse(true);
         range.moveEnd('character', end);
         range.moveStart('character', start);
         range.select();
      }
   });
};
(function($) {
   $.fn.getCursorPosition = function() {
      var input = this.get(0);
      if (!input) return; // No (input) element found
      if ('selectionStart' in input) {
         // Standard-compliant browsers
         return input.selectionStart;
      } else if (document.selection) {
         // IE
         input.focus();
         var sel = document.selection.createRange();
         var selLen = document.selection.createRange().text.length;
         sel.moveStart('character', -input.value.length);
         return sel.text.length - selLen;
      }
   }
})(jQuery);
