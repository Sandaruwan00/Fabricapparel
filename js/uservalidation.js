$(document).ready(function () {
  var today = new Date();
  today.setFullYear(today.getFullYear() - 18);

  var maxDate = today.toISOString().split("T")[0];
  $("#dob").attr("max", maxDate);

  $("#user_role").change(function () {
    var role_id = $("#user_role").val();
    var url = "../controller/user_controller.php?status=load_functions";

    $.post(url, { role: role_id }, function (data) {
      $("#display_functions").html(data).show();
    });
  });

  $("form").submit(function () {
    var fname = $("#fname").val();
    var lname = $("#lname").val();
    var email = $("#email").val();
    var dob = $("#dob").val();
    var nic = $("#nic").val();
    var cno1 = $("#cno1").val();
    var cno2 = $("#cno2").val();
    var user_role = $("#user_role").val();

    if (fname == "") {
      $("#msg").html("First Name Cannot Be Empty!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }
    if (lname == "") {
      $("#msg").html("Last Name Cannot Be Empty!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }
    if (dob == "") {
      $("#msg").html("Date of Birth Cannot Be Empty!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }

    var dobDate = new Date(dob);
    var today = new Date();

    var age = today.getFullYear() - dobDate.getFullYear();
    var monthDiff = today.getMonth() - dobDate.getMonth();

    if (
      monthDiff < 0 ||
      (monthDiff === 0 && today.getDate() < dobDate.getDate())
    ) {
      age--;
    }

    if (age < 18) {
      $("#msg").html("User must be at least 18 years old!");
      $("#msg").removeClass("alert-success").addClass("alert alert-danger");
      return false;
    }

    if (nic == "") {
      $("#msg").html("NIC Cannot Be Empty!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }
    if (email == "") {
      $("#msg").html("Email Cannot Be Empty!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }

    if (cno1 == "") {
      $("#msg").html("Contact Mobile Cannot Be Empty!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }
    if (cno2 == "") {
      $("#msg").html("Contact Fixed Cannot Be Empty!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }
    if (user_role == "") {
      $("#msg").html("User Role Cannot Be Empty!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }

    var patNic = /^[0-9]{9}[vVxX]$/;
    var patNic2 = /^[0-9]{12}$/;
    var patmobile = /^07[0-9]{8}$/;
    var patfixed =
      /^0(11|21|23|24|25|26|27|31|33|34|35|36|37|38|41|45|47|51|52|54|55|57|63|65|66|67|81|91)[0-9]{7}$/;

    if (!nic.match(patNic) && !nic.match(patNic2)) {
      $("#msg").html("NIC is invalid!!!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }
    if (!cno1.match(patmobile)) {
      $("#msg").html("Contact Mobile is invalid!!!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }
    if (!cno2.match(patfixed)) {
      $("#msg").html("Contact Fixed is invalid!!!");
      $("#msg").addClass("alert alert-danger");
      return false;
    }
  });
});
