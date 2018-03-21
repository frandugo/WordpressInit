$(function () {
    $("#from").datepicker({
      dateFormat: 'yy-mm-dd',
      onClose: function (selectedDate) {
        $("#to").datepicker("option", "minDate", selectedDate);
      }
    });
    $("#to").datepicker({
      dateFormat: 'yy-mm-dd',
      onClose: function (selectedDate) {
        $("#from").datepicker("option", "maxDate", selectedDate);
      }
    });
});