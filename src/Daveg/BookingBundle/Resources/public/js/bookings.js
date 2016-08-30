(function(){

var app = angular.module('bookings', ['booking-tabs', 'booking-form', 'booking-grid']).config(function($interpolateProvider){
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});

app.service('BookingLoader', function($q, $timeout, $http) {

  var bookings = [];

  function addBooking(booking) {

    booking.startDate = booking.start_date;
    booking.endDate = booking.end_date;
    
    bookings.push(booking);
  }

  var getBookings = function() {

    var deferred = $q.defer();

    $http.get('/booking/load/all.json').success(function(response) {
      bookings = response;
      deferred.resolve(response);
    });

    return deferred.promise;

  };

  return {
    addBooking: addBooking,
    getBookings: getBookings
  };

});

})();
