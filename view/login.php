<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login | Fabric Apparel (PVT) LTD.</title>
    <?php include_once '../includes/bootstrap_css_includes.php'; ?>
</head>

<body>
    <div class="container">
        <form action="../controller/login_controller.php?status=login" method="post">


            <div class="row justify-content-center" style="margin-top:25px; height: 80px;">
                <div id="msg" class="col-md-4">
                    <?php if (isset($_GET["msg"])) { ?>
                        <div class="alert alert-danger text-center">
                            <?php echo base64_decode($_GET["msg"]); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>


            <div class="row justify-content-center">
                <div class="col-md-6 p-4"
                    style="background: linear-gradient(180deg, rgba(0, 141, 196, 1) 0%, rgba(255, 204, 133, 1) 100%);
                            border-radius:20px; box-shadow: 10px 10px 10px grey;">

                    <div class="row">
                        <div class="col-md-6 text-center">
                            <img src="../images/logo/logo_transparent.png" alt="fabricapparellogo" class="img-fluid">
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3 text-center">
                                <label class="form-label"
                                    style="font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:25px;">
                                    Login to your account
                                </label>
                            </div>


                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="email" id="loginusername" name="loginusername"
                                    class="form-control" placeholder="Email">
                            </div>


                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" id="loginpassword" name="loginpassword"
                                    class="form-control" placeholder="Password">
                            </div>


                            <div class="d-grid">
                                <button type="submit" name="submit" class="btn btn-primary fw-bold">
                                    Login
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>


</body>

<script src="../js/jquery-3.7.1.js"></script>

<script>
    const msg = document.getElementById('msg');

    const delayTime = 3000;

    setTimeout(() => {
        msg.style.display = 'none';
    }, delayTime);
</script>

</html>