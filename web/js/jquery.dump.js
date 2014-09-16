/*
 *  Made by Tomas van Rijsse - Pionect at Rotterdam, Netherlands
 *  
 *  This work is licensed under a Creative Commons Attribution 3.0 Unported License.
 *  http://creativecommons.org/licenses/by/3.0/
 *  
 *  The result will be equivalent to the PHP function print_r.
 *  echo '<pre>' . print_r($data) . '</pre>';
 *  
 *  USAGE: 
 *  var data = [{'id':1,'name':'hello'},'world'];
 *  $('#element').dump(data);
 */ 

(function($){	  
    $.fn.dump = function(variable){
        return this.each(function(){
        if(typeof variable == 'object'){
            var string = $.dump.objectToString(variable,0);
            $(this).html(string);
        } else {
            $(this).html('<pre>'+variable.toString()+'</pre>');
        }
	});
}

$.dump = {
	objectToString : function (variable,i){
          var string = '';
          if(typeof variable == 'object' && i < 3){ // 3 is to prevent endless recursion, set higher for more depth
              string += 'Object ( <ul style="list-style:none;">';
              var key;
              for(key in variable) {
                  if (variable.hasOwnProperty(key)) {
                    string += '<li>['+key+'] => ';
                    string += $.dump.objectToString(variable[key],i+1);
                    string += '</li>';
                  }
              }
              string += '</ul> )';
          } else {
              string = variable.toString();
          }
          return string;
    }
}
})(jQuery)