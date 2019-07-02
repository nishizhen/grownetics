$(document).ready(function() {
    $.each($('.noteCreatedDate'), function(ind, val) {
        var date = moment.unix($(val).text()).format("ddd, MMM Do YYYY, h:mm a");
        $(val).text(date);
    });
});