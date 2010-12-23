window.addEvent('domready', function() {
  drumon.csrf_token = document.getElement('meta[name=csrf-token');
  drumon.applyEvents();
});

(function($) {
  window.drumon = {
    applyEvents: function() {
      $$('a[data-method]').addEvent('click',function(e){
				e.stop();
				if(drumon.confirmed(this)) {
		      var form = new Element('form', {
		        method: 'post',
		        action: this.get('href'),
		        styles: { display: 'none' }
		      }).inject(this, 'after');

		      var methodInput = new Element('input', {
		        type: 'hidden',
		        name: '_method',
		        value: this.get('data-method')
		      });

		      var csrfInput = new Element('input', {
		        type: 'hidden',
		        name: '_token',
		        value: drumon.csrf_token
		      });
		
		      form.adopt(methodInput, csrfInput).submit();
		    }
			});
			
			$$(':not([data-method])[data-confirm]').addEvent('click',function(e){
				return drumon.confirmed(this);
			});
    },

    confirmed: function(el) {
      var confirmMessage = el.get('data-confirm');
      if(confirmMessage && !confirm(confirmMessage)) {
        return false;
      }
      return true;
    }
  };
})(document.id);