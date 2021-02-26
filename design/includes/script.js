$(document).ready(function(){ //check if document is ready (page is being shown)
    $("#filterInput").on("keyup", function() { //if textfield with id filterinput has a keystroke, search in table
      var value = $(this).val().toLowerCase();
      $("#filterTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    $('table').tablesort(); //sort table filtertable
  });

  $(document).ready(function(){ //if error show error modal
    $("#error").modal('show');
  });
  $(document).on('hidden.bs.modal','#error', function () { //on hide of error modal, refresh page to clear message
    window.location = window.location.href;
  });
  
  $(document).ready(function(){ //if success show success modal
    $("#success").modal('show');
  });
  $(document).on('hidden.bs.modal','#success', function () { //on hide of success modal, refresh page to clear message
    window.location = window.location.href;
  });