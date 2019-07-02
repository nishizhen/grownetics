 $(function () {
    $("#date-popover").popover({html: true, trigger: "manual"});
    $("#date-popover").hide();
    $("#date-popover").click(function (e) {
        $(this).hide();
    });
    if (typeof(taskDates) != "undefined") {
        var eventData = taskDates;

        $("#my-calendar").zabuto_calendar({
            legend: [
                {type: "text", label: "Active Tasks", badge: eventData.length},
            ],
            today: true,
            show_days: false,
            data: eventData,
            nav_icon: {
                prev: '<i class="fa fa-chevron-circle-left"></i>',
                next: '<i class="fa fa-chevron-circle-right"></i>'
            },
            action: function () {
                if ($(this).hasClass('event')) {
                    return myDateFunction(this.id, true, this.title);
                } else {
                    return false;
                }

            },
            action_nav: function () {
                return myNavFunction(this.id);
            }


        });

        function myDateFunction(id, fromModal, title) {
            $("#date-popover").hide();
            if (fromModal) {
                $("#" + id + "_modal").modal("hide");
            }

            var date = $("#" + id).data("date");
            var hasEvent = $("#" + id).data("hasEvent");
            if (hasEvent && !fromModal) {
                return false;
            }
            $("#date-popover-content").html('You clicked on date ' + date);
            $("#date-popover").show();
            if (title != 0) {
                return window.location.assign("/harvestBatches/view/" + title);
            } else {
                return window.location.assign("/tasks");
            }
            
        }

        function myNavFunction(id) {
            $("#date-popover").hide();
            var nav = $("#" + id).data("navigation");
            var to = $("#" + id).data("to");
        }
    }
});