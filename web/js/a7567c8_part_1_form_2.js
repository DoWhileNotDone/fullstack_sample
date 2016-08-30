(function(){

var app = angular.module('booking-form', []);

app.directive('bookingForm', function(){
    return {
      restrict: 'E',
      templateUrl: '/booking/template/form',
      controller: ['$scope', '$http', '$timeout', 'BookingLoader', function($scope, $http, $timeout, BookingLoader) {

        var formCtrl = this;

        formCtrl.newbooking = {};

        formCtrl.notification_message = '';

        formCtrl.renderDateTimePicker = function() {

          var $fp1 = $( ".filthypillow-1" ),
              now = moment( ).subtract( "seconds", 1 );
              $fp1.filthypillow( {
                minDateTime: function( ) {
                return now;
              }
          } );
          $fp1.on( "focus", function( ) {
            $fp1.filthypillow( "show" );
          } );
          $fp1.on( "fp:save", function( e, dateObj ) {
            $fp1.val( dateObj.format( "DD/MM/YYYY HH:mm" ) );
            $fp1.filthypillow( "hide" );

            formCtrl.newbooking.start_date = dateObj.format( "DD/MM/YYYY HH:mm" );
            $scope.$apply();

          } );

          var $fp2 = $( ".filthypillow-2" ),
              now = moment( ).subtract( "seconds", 1 );
              $fp2.filthypillow( {
                minDateTime: function( ) {
                return now;
              }
          } );
          $fp2.on( "focus", function( ) {
            $fp2.filthypillow( "show" );
          } );
          $fp2.on( "fp:save", function( e, dateObj ) {
            $fp2.val( dateObj.format( "DD/MM/YYYY HH:mm" ) );
            $fp2.filthypillow( "hide" );

            formCtrl.newbooking.end_date = dateObj.format( "DD/MM/YYYY HH:mm" );
            $scope.$apply();
          } );

        }

        formCtrl.renderDateTimePicker();

        formCtrl.addBooking = function() {
          BookingLoader.addBooking(formCtrl.newbooking);
        };

        formCtrl.setNotification = function(message) {

          //Success Booking Added Notification
          formCtrl.notification_message = message;

          $timeout(function(){
             formCtrl.notification_message = '';
          }, 4000);

        };

        this.submitForm = function() {

          //Submit New Booking to Symfony to persist
          $http.post('/booking/insert/',formCtrl.newbooking).success(function(response) {

            formCtrl.setNotification(response);

            formCtrl.addBooking();
            //Clear booking following successful submission
            formCtrl.clearBookingForm();

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
