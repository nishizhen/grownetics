$('.submitAddPlants').on('click', function () {
  $('.plantsSpinner').show();
  $.ajax({
    url: "/plants/add",
    type: 'post',
    data: $('#newPlantsForm').serialize(),
    success: function (data) {
      $('.plantsSpinner').hide();
      window.location.reload();
    },
    error: function (data) {
      $('.plantsSpinner').hide();
      $.gritter.add({
        title: 'Warning',
        text: data.responseText,
        class_name: 'gritter-light',
        time: '7500'
      });
    }
  });
});
