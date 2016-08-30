(function(){

  var app = angular.module('booking-tabs', []);

  app.directive('bookingTabs', function(){
      return {
        restrict: 'E',
        templateUrl: '/booking/template/tabs',
        controller: function() {
          this.tab = 1;

          this.selectTab = function(setTab) {
            this.tab = setTab;
          };

          this.isSelectedTab = function(tabIndex) {
            return this.tab === tabIndex;
          };

        },
        controllerAs: 'tabpanel',
      };

  });

})();
