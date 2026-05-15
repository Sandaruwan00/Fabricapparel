<?php

include_once '../commons/db_connection.php';

$dbcon = new DbConnection();

class Price
{

    public function addProductType($product_type_name)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO product_type (product_type_name) VALUES ('$product_type_name')";
        $con->query($sql) or die($con->error);
        $product_type_name_id = $con->insert_id;
        return $product_type_name_id;
    }

    public function getAllProductType()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM product_type WHERE product_type_status != -1";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function deleteProductType($product_type_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE product_type SET product_type_status='-1' WHERE product_type_id='$product_type_id';";
        $result = $con->query($sql) or die($con->error);
    }

    public function updateProductType($product_type_id, $product_type_name)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE product_type SET product_type_name='$product_type_name' WHERE product_type_id='$product_type_id'";
        $con->query($sql) or die($con->error);
    }

    public function activateProductType($product_type_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE product_type SET product_type_status='1' WHERE product_type_id='$product_type_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function deactivateProductType($product_type_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE product_type SET product_type_status='0' WHERE product_type_id='$product_type_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function getAllSizes()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM sizing WHERE size_status != -1";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    function addSize($size_name, $size_short_name)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO sizing (size_name , size_short_name) VALUES ('$size_name','$size_short_name')";
        $con->query($sql) or die($con->error);
        $size_id  = $con->insert_id;
        return $size_id;
    }

    public function deleteSize($size_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE sizing SET size_status='-1' WHERE size_id='$size_id';";
        $result = $con->query($sql) or die($con->error);
    }

    public function updateSize($size_id, $size_name, $size_short_name)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE sizing SET size_name='$size_name' , size_short_name='$size_short_name' WHERE size_id='$size_id'";
        $con->query($sql) or die($con->error);
    }

    public function activateSize($size_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE sizing SET size_status='1' WHERE size_id='$size_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function deactivateSize($size_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE sizing SET size_status='0' WHERE size_id='$size_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function getAllPricing()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM pricing p, sizing s, product_type pt WHERE pt.product_type_id = p.product_type_id AND s.size_id = p.size_id AND pricing_status != -1";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function checkExistingPrice($product_id, $size_id)
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT * FROM pricing WHERE product_type_id = '$product_id' AND size_id = '$size_id'";
        $result = $con->query($sql);
        return $result->num_rows;
    }

    public function addPricing($select_product, $select_size, $price, $size_chart)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO pricing(product_type_id, size_id, price, size_chart_image) VALUES ('$select_product', '$select_size', '$price', '$size_chart')";
        $con->query($sql) or die($con->error);
        $price_id = $con->insert_id;
        return $price_id;
    }

    public function deletePrice($price_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE pricing SET pricing_status='-1' WHERE price_id='$price_id';";
        $result = $con->query($sql) or die($con->error);
    }

    public function activatePrice($price_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE pricing SET pricing_status='1' WHERE price_id='$price_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function deactivatePrice($price_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE pricing SET pricing_status='0' WHERE price_id='$price_id'";
        $result = $con->query($sql) or die($con->error);
    }

    public function getActiveProductTypes()
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT * FROM product_type WHERE product_type_status = 1";
        return $con->query($sql);
    }

    public function getActiveSizes()
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT * FROM sizing WHERE size_status = 1";
        return $con->query($sql);
    }

    public function getPrice($price_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM pricing p, sizing s, product_type pt WHERE p.product_type_id = pt.product_type_id AND p.size_id = s.size_id AND price_id='$price_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function updatePrice($price_id, $price, $img)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE pricing SET price='$price' , size_chart_image='$img' WHERE price_id='$price_id'";
        $con->query($sql) or die($con->error);
    }

    public function getFilteredPrices($product_types = [], $sizes = [], $status = [])
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM pricing p INNER JOIN product_type pt ON p.product_type_id = pt.product_type_id INNER JOIN sizing s ON p.size_id = s.size_id WHERE 1=1";

        if (!empty($product_types)) {
            $ids = implode(",", array_map('intval', $product_types));
            $sql .= " AND p.product_type_id IN ($ids)";
        }

        if (!empty($sizes)) {
            $ids = implode(",", array_map('intval', $sizes));
            $sql .= " AND p.size_id IN ($ids)";
        }

        if (!empty($status)) {
            if (!(in_array("0", $status) && in_array("1", $status))) {
                $ids = implode(",", array_map('intval', $status));
                $sql .= " AND p.pricing_status IN ($ids)";
            }
        }

        $result = $con->query($sql) or die($con->error);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}
