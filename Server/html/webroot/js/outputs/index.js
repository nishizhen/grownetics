var prevScheduleValue;
$('.outputSchedule').focus(function(ev) {
    console.log(this,ev,$(this).val())
    prevScheduleValue = $(this).val()
})
$('.outputSchedule').change(function(ev){
    if (!confirm("Are you sure you want to change this schedule?")) {
        $(this).val(prevScheduleValue)
        console.log("revert to ",prevScheduleValue)
        return false
    }
    window.location.pathname = '/outputs/setSchedule/' + ev.currentTarget.dataset.id + '/' + ev.currentTarget.selectedOptions[0].value
});

$('.thresholdSet').change(function(ev) {
    $.ajax({
        url: "/RuleConditions/edit/"+ev.currentTarget.name+'/'+ev.currentTarget.className.split(" ")[1],
        type: 'patch',
        data: {"value":ev.currentTarget.value},
        success: function(res) {
            $.gritter.add({
                title: 'Updated Co2 Rule!',
                text: 'The Co2 Rule has been updated successfully.',
            });
        }
    });
});