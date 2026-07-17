$(document).ready(function () {

    $("form").submit(function () {

        var company_id = $("#company_id_input").val().trim();
        var order_date = $("#todayDate").val().trim();
        var address_line_1 = $("#address_line_1").val().trim();
        var address_line_2 = $("#address_line_2").val().trim();
        var address_line_3 = $("#address_line_3").val().trim();
        var district = $("#district").val();
        var delivery_date = $("#delivery_date").val().trim();
        var payment_method = $("#payment_method").val();

        if (company_id == "") {
            $("#msg").html("Please Select a Buyer!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (order_date == "") {
            $("#msg").html("Order Date Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (orderItems.length === 0) {
            $("#msg").html("Please Add At Least One Order Item!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (address_line_1 == "") {
            $("#msg").html("Delivery Address Line 1 Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (address_line_2 == "") {
            $("#msg").html("Delivery Address Line 2 Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (address_line_3 == "") {
            $("#msg").html("Delivery Address Line 3 Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (district == "") {
            $("#msg").html("Please Select a District!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (delivery_date == "") {
            $("#msg").html("Expected Delivery Date Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        

        // Serialize order items into the hidden input right before submit
        document.getElementById("order_items_input").value = JSON.stringify(orderItems);

    });

});