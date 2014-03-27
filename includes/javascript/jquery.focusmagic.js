// Copyright ©2011 Aaron Vanderzwan, by Aaron Vanderzwan
// 
// LICENSE
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
// 
// VERSION: 1.5



(function($) {
  // Plugin info
  $.fn.focusMagic = function(options) {
	    var form = $(this);
    
		// Dump if not wanted
		if(form.hasClass('ignore')) return;

		// On load
		form.find('label').not('.ignore').each(function(i){
			var $this = $(this);
			
			// If any parent has ignore class, ignore this
			if($this.parents('.ignore').length > 0) return true;
			
			// Remove the label from the viewport, but leave it visible for screen readers
			$this.css({'position':'absolute','left':'-9999px'});
			
			// Get the id the label is for
			var id = $this.attr('for');
			var $id = $('#'+id);
			
			// Password Fields have to be treated differently
			if($id.attr('type') == 'password'){
				$id.after('<input type="text" class="FMREP '+$id.attr('class')+' id-'+id+'" value="'+$this.html()+'" />');
				$id.next('input').hide();
			}
			
			// if the value is not set in the HTML (from the server)
			if($id.val().length == 0){
				if($id.attr('type') == 'password'){
					$id.hide().next('input').show();
				}else{
					$id.val($this.html());
				}
			}
		});
		
		// On focus
	    form.find('input,textarea').focus(function(){
			var $this = $(this);
			var content = $this.val(),
				$label = $this.parent().find('label[for='+this.id+']');
			
			if($this.hasClass('FMREP')){
				$this.hide();
				var id = $this.attr('class').split('id-')[1].split(' ')[0];
				$('#'+id).show().focus();
			}else if(content == $label.html()){
				$(this).val('');
			}
		
			// On Blur
	       	$this.blur(function(){
	        	if( $this.val() ==  '' && $label.hasClass('ignore') == false){
					if($this.attr('type') == 'password'){
						$this.hide().next('.FMREP').show();
					}else{
						$this.val($label.html());
					}
	       		}
	       	});
	    });
	
		// On submit, if values are default values, make them ''.
		form.submit(function(){
			form.find('label').not('.ignore').each(function(){
				var $this = $(this);
				
				// If any parent has ignore class, ignore this
				if($this.parents('.ignore').length > 0) return true;
				
				// Get the id the label is for
				var id = $this.attr('for');
			
				// if the value is default (what we set)
				if($('#'+id).val() == $this.html()){
					$('#'+id).val('');
				}
			});
		});
    
	    // private function for debugging
	    function debug($obj) {
	      if (window.console && window.console.log) {
	        window.console.log($obj);
	      }
	    }
  };
})(jQuery);