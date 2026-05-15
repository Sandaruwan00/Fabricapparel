<?php
include '../commons/session.php';
if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}
$status = $_GET["status"];
include '../model/price_model.php';
$priceObj = new Price();
switch ($status) {
    case "add_product_type":
        $product_type = $_POST["product_type"];
        try {
            $product_type_id = $priceObj->addProductType($product_type);

            if ($product_type_id > 0) {

                $msg = "$product_type Successfully Added";
                $msg = base64_encode($msg);

    ?>
                <script>
                    window.location = "../view/product-type.php?msg=<?php echo $msg; ?>";
                </script>
            <?php
            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
            ?>
            <script>
                window.location = "../view/product-type.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "delete_product_type":
        $product_type_id = $_GET["product_type_id"];
        $product_type_id = base64_decode($product_type_id);
        $priceObj->deleteProductType($product_type_id);
        $msg = "Successfully Deleted!!!";
        $msg = base64_encode($msg);
        ?>

        <script>
            window.location = "../view/product-type.php?msg=<?php echo $msg; ?>";
        </script>

        <?php




        break;

    case "update_product_type":

        $product_type_id = $_POST["product_type_id"];
        $product_type_name = $_POST["product_type_name"];

        try {

            $priceObj->updateProductType($product_type_id, $product_type_name);

            $msg = "$product_type_name Successfully Updated!";
            $msg = base64_encode($msg);
        ?>

            <script>
                window.location = "../view/product-type.php?msg=<?php echo $msg; ?>";
            </script>

        <?php

        } catch (Exception $ex) {

            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>

            <script>
                window.location = "../view/product-type.php?msg=<?php echo $msg; ?>";
            </script>

        <?php
        }

        break;

    case "activate_product_type":

        $product_type_id = $_GET["product_type_id"];
        $product_type_id = base64_decode($product_type_id);
        $priceObj->activateProductType($product_type_id);
        $msg = "Successfully Activated!!!";
        $msg = base64_encode($msg);
        ?>

        <script>
            window.location = "../view/product-type.php?msg=<?php echo $msg; ?>";
        </script>

    <?php

        break;


    case "deactivate_product_type":

        $product_type_id = $_GET["product_type_id"];
        $product_type_id = base64_decode($product_type_id);
        $priceObj->deactivateProductType($product_type_id);
        $msg = "Successfully Deactivated!!!";
        $msg = base64_encode($msg);
    ?>

        <script>
            window.location = "../view/product-type.php?msg=<?php echo $msg; ?>";
        </script>

        <?php




        break;

    case "add_size":
        $size_name = $_POST["size_name"];
        $size_short_name = $_POST["size_short_name"];
        try {
            $size_id = $priceObj->addSize($size_name, $size_short_name);

            if ($size_id > 0) {

                $msg = "$size_name  Successfully Added";
                $msg = base64_encode($msg);

        ?>
                <script>
                    window.location = "../view/sizing.php?msg=<?php echo $msg; ?>";
                </script>
            <?php
            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
            ?>
            <script>
                window.location = "../view/sizing.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "delete_size":
        $size_id  = $_GET["size_id"];
        $size_id  = base64_decode($size_id);
        $priceObj->deleteSize($size_id);
        $msg = "Successfully Deleted!!!";
        $msg = base64_encode($msg);
        ?>

        <script>
            window.location = "../view/sizing.php?msg=<?php echo $msg; ?>";
        </script>

        <?php




        break;

    case "update_size":

        $size_id = $_POST["size_id"];
        $size_name = $_POST["size_name"];
        $size_short_name = $_POST["size_short_name"];

        try {

            $priceObj->updateSize($size_id, $size_name, $size_short_name);

            $msg = "$size_name Successfully Updated!";
            $msg = base64_encode($msg);
        ?>

            <script>
                window.location = "../view/sizing.php?msg=<?php echo $msg; ?>";
            </script>

        <?php

        } catch (Exception $ex) {

            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>

            <script>
                window.location = "../view/sizing.php?msg=<?php echo $msg; ?>";
            </script>

        <?php
        }

        break;

    case "activate_size":

        $size_id = $_GET["size_id"];
        $size_id = base64_decode($size_id);
        $priceObj->activateSize($size_id);
        $msg = "Successfully Activated!!!";
        $msg = base64_encode($msg);
        ?>

        <script>
            window.location = "../view/sizing.php?msg=<?php echo $msg; ?>";
        </script>

    <?php

        break;


    case "deactivate_size":

        $size_id = $_GET["size_id"];
        $size_id = base64_decode($size_id);
        $priceObj->deactivateSize($size_id);
        $msg = "Successfully Deactivated!!!";
        $msg = base64_encode($msg);
    ?>

        <script>
            window.location = "../view/sizing.php?msg=<?php echo $msg; ?>";
        </script>

        <?php




        break;

    case "add_price":
        $select_product = $_POST["select_product"];
        $select_size = $_POST["select_size"];
        $price = $_POST["price"];

        $exists = $priceObj->checkExistingPrice($select_product, $select_size);

        if ($exists > 0) {

            $msg = "This product and size already has a price!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/price.php?msg=<?php echo $msg; ?>";
            </script>
            <?php
            break;
        }


        $size_chart = "";

        if (isset($_FILES["size_chart"]) && $_FILES["size_chart"]["name"] != "") {
            $file_name = time() . "_" . $_FILES["size_chart"]["name"];
            $path = "../images/size_charts/" . $file_name;

            move_uploaded_file($_FILES["size_chart"]["tmp_name"], $path);

            $size_chart = $file_name;
        }

        try {


            $price_id = $priceObj->addPricing($select_product, $select_size, $price, $size_chart);

            if ($price_id > 0) {

                $msg = "Successfully Added";
                $msg = base64_encode($msg);

            ?>
                <script>
                    window.location = "../view/price.php?msg=<?php echo $msg; ?>";
                </script>
            <?php
            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
            ?>
            <script>
                window.location = "../view/add-price.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "delete_price":
        $price_id = $_GET["price_id"];
        $price_id = base64_decode($price_id);
        $priceObj->deletePrice($price_id);
        $msg = "Successfully Deleted!!!";
        $msg = base64_encode($msg);
        ?>

        <script>
            window.location = "../view/price.php?msg=<?php echo $msg; ?>";
        </script>

    <?php




        break;

    case "activate_price":

        $price_id = $_GET["price_id"];
        $price_id = base64_decode($price_id);
        $priceObj->activatePrice($price_id);
        $msg = "Successfully Activated!!!";
        $msg = base64_encode($msg);
    ?>

        <script>
            window.location = "../view/price.php?msg=<?php echo $msg; ?>";
        </script>

    <?php

        break;


    case "deactivate_price":

        $price_id = $_GET["price_id"];
        $price_id = base64_decode($price_id);
        $priceObj->deactivatePrice($price_id);
        $msg = "Successfully Deactivated!!!";
        $msg = base64_encode($msg);
    ?>

        <script>
            window.location = "../view/price.php?msg=<?php echo $msg; ?>";
        </script>

        <?php




        break;




    case "update_price":

        $price_id = $_POST["price_id"];

        $product_type_id  = $_POST["select_product"];
        $size_id = $_POST["select_size"];
        $price = $_POST["price"];




        $size_chart = $_FILES["size_chart"];


        try {


            $priceResult = $priceObj->getPrice($price_id);
            $pricerow = $priceResult->fetch_assoc();

            $prev_image = $pricerow["size_chart_image"];

            $img = $prev_image;

            if (isset($_FILES["size_chart"]) && $_FILES["size_chart"]["name"] != "") {

                $img = time() . "_" . $_FILES["size_chart"]["name"];
                $path = "../images/size_charts/";

                move_uploaded_file($_FILES["size_chart"]["tmp_name"], $path . $img);

                if (!empty($prev_image) && file_exists($path . $prev_image)) {
                    unlink($path . $prev_image);
                }
            }

            $priceObj->updatePrice($price_id, $price, $img);




            $msg = "Successfully Updated!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/price.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/edit-price.php?msg=<?php echo $msg; ?>";
            </script>
<?php

        }

        break;

    case "generate_report":
        // 1. Collect filter values from POST
        $product_types = $_POST['product_type'] ?? [];
        $sizes         = $_POST['size'] ?? [];
        $status        = $_POST['status'] ?? [];
        $include_size_chart = $_POST['include_size_chart'] ?? 0;



        // Filtered data based on checkboxes
        $data = $priceObj->getFilteredPrices($product_types, $sizes, $status);

        // 3. Include the PDF report file
        // Pass $data and $include_size_chart to the report
        include '../view/generate_price_report_file.php';
        break;

}


?>