$(document).ready(function(){
    $("#filterInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#filterTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    $('table').tablesort();
  });
