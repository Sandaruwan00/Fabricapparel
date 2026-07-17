$(document).ready(function () {

    $("form").submit(function () {

        var company_name = $("#company_name").val().trim();
        var company_registration = $("#company_registration").val().trim();
        var business_type = $("#business_type").val();
        var website = $("#website").val().trim();
        var company_address_line_1 = $("#company_address_line_1").val().trim();
        var company_city = $("#company_city").val().trim();
        var company_postal_code = $("#company_postal_code").val().trim();
        var company_country = $("#company_country").val().trim();

        var contact_name = $("#contact_name").val().trim();
        var job_title = $("#job_title").val().trim();
        var contact_email = $("#contact_email").val().trim();
        var contact_nic = $("#contact_nic").val().trim();
        var contact_phone = $("#contact_phone").val().trim();
        var office_address_line_1 = $("#office_address_line_1").val().trim();
        var office_address_line_2 = $("#office_address_line_2").val().trim();
        var office_address_line_3 = $("#office_address_line_3").val().trim();

        if (company_name == "") {
            $("#msg").html("Company Name Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (company_registration == "") {
            $("#msg").html("Company Registration Number Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (business_type == "") {
            $("#msg").html("Please Select a Business Type!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (website == "") {
            $("#msg").html("Website Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (company_address_line_1 == "") {
            $("#msg").html("Company Address Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (company_city == "") {
            $("#msg").html("City Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (company_postal_code == "") {
            $("#msg").html("Postal Code Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (company_country == "") {
            $("#msg").html("Country Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (contact_name == "") {
            $("#msg").html("Contact Person Name Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (job_title == "") {
            $("#msg").html("Job Title Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (contact_email == "") {
            $("#msg").html("Email Address Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (contact_nic == "") {
            $("#msg").html("NIC Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (contact_phone == "") {
            $("#msg").html("Phone Number Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (office_address_line_1 == "") {
            $("#msg").html("Office Address Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (office_address_line_2 == "") {
            $("#msg").html("Office Address Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (office_address_line_3 == "") {
            $("#msg").html("Office Address Cannot Be Empty!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        var patNic = /^[0-9]{9}[vVxX]$/;
        var patNic2 = /^[0-9]{12}$/;
        var patMobile = /^07[0-9]{8}$/;
        var patEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var patWebsite = /^(https?:\/\/)?([\w-]+\.)+[\w-]{2,}(\/.*)?$/i;
        var patPostal = /^[0-9]{5}$/;

        if (!contact_nic.match(patNic) && !contact_nic.match(patNic2)) {
            $("#msg").html("NIC is Invalid!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (!contact_phone.match(patMobile)) {
            $("#msg").html("Phone Number is Invalid!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (!contact_email.match(patEmail)) {
            $("#msg").html("Email Address is Invalid!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (!website.match(patWebsite)) {
            $("#msg").html("Website URL is Invalid!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

        if (!company_postal_code.match(patPostal)) {
            $("#msg").html("Postal Code is Invalid!");
            $("#msg").addClass("alert alert-danger");
            return false;
        }

    });

});