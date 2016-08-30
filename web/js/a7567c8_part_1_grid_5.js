(function(){

var app = angular.module('booking-grid', []);

app.directive('bookingsView', function(){
    return {
      restrict: 'E',
      templateUrl: '/booking/template/grid',
      controller: ['$scope', '$http', 'BookingLoader', function($scope, $http, BookingLoader) {

        $scope.bookings = [];

        BookingLoader.getBookings().then(function(bookings) {
          $scope.bookings = bookings;
        });

      }],
      controllerAs: 'gridCtrl',
    };

});


})();
