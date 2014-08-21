(function(ko){
  // Wrap ko.observable and ko.observableArray
  var methods = ['observable', 'observableArray'];

  ko.utils.arrayForEach(methods, function(method){
    var saved = ko[method];
    
    ko[method] = function(initialValue, options){
      options = options || {};

      var key = options.persist;

      // Load existing value if set
      if(key && localStorage.hasOwnProperty(key)){
        try{
          initialValue = JSON.parse(localStorage.getItem(key))
        }catch(e){};
      }

      // Create observable from saved method
      var observable = saved(initialValue);

      // Subscribe to changes, and save to localStorage
      if(key){
        observable.subscribe(function(newValue){
          localStorage.setItem(key, ko.toJSON(newValue));
        });
      };

      return observable;
    }
  })
})(ko);
