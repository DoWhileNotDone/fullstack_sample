(function(){

var app = angular.module('booking-form', []);

app.directive('bookingForm', function(){
    return {
      restrict: 'E',
      templateUrl: '/booking/template/form',
      controller: ['$scope', '$http', '$timeout', 'BookingLoader', function($scope, $http, $timeout, BookingLoader) {

        var bookingCtrl = this;

        this.newbooking = {};
        this.notification_message = '';

        bookingCtrl.addBooking = function() {
          BookingLoader.addBooking(this.newbooking);
        };

        bookingCtrl.setNotification = function(message) {

          //Success Booking Added Notification
          bookingCtrl.notification_message = message;

          $timeout(function(){
             bookingCtrl.notification_message = '';
          }, 4000);

        };

        this.submitForm = function() {

          //Submit New Booking to Symfony to persist
          $http.post('/booking/insert/',this.newbooking).success(function(response) {

            bookingCtrl.setNotification(response);

            bookingCtrl.addBooking();
            //Clear booking following successful submission
            bookingCtrl.clearBookingForm();

          });

        }

        this.clearBookingForm = function() {
          this.newbooking = {};
          //Reset validation classes
          $scope.addBooking.$setPristine();

        }

        this.hasNotificationMessage = function() {
          return this.notification_message !== '';
        }

      }],
      controllerAs: 'formCtrl',
    };

});

})();
