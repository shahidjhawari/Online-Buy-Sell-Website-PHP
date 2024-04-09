<?php
require('top.php');

if (!isset($_SESSION['USER_LOGIN'])) {
?>
    <script>
        window.location.href = 'index.php';
    </script>
    <?php
    exit;
}

$user_id = $_SESSION['USER_ID'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $con->real_escape_string($_POST['fullName']);
    $product_name = $con->real_escape_string($_POST['productName']);
    $price = $con->real_escape_string($_POST['price']);
    $detail = $con->real_escape_string($_POST['detail']);
    $select_option = $con->real_escape_string($_POST['select1']);
    $phone_number = $con->real_escape_string($_POST['phoneNumber']);
    $address = $con->real_escape_string($_POST['address']);

    if ($uploaded_images_count < 3) {
        echo "<script>alert('Please upload minimum 3 images.'); window.location.href = 'post.php';</script>";
        exit;
    } else {

    $image_names = array();

    $uploaded_images_count = 0;

    for ($i = 1; $i <= 5; $i++) {
        if ($_FILES["fileToUpload$i"]["size"] > 0) {
            $target_dir = PRODUCT_IMAGE_SERVER_PATH;
            $target_file = $target_dir . basename($_FILES["fileToUpload$i"]["name"]);
            $image_name = basename($_FILES["fileToUpload$i"]["name"]);

            if (move_uploaded_file($_FILES["fileToUpload$i"]["tmp_name"], $target_file)) {
                $image_names[] = $image_name;
                $uploaded_images_count++;
            }
        }
    }


        $image_columns = implode(", ", array_map(function ($index) {
            return "image$index";
        }, range(1, count($image_names))));

        $image_values = "'" . implode("', '", $image_names) . "'";

        $sql = "INSERT INTO post (user_id, full_name, product_name, price, detail, select_option, phone_number, address, $image_columns)
            VALUES ('$user_id', '$full_name', '$product_name', $price, '$detail', '$select_option', '$phone_number', '$address', $image_values)";

        if ($con->query($sql) === TRUE) { ?>
            <script>
                window.location.href = "home.php"
            </script>
<?php } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $_SESSION['form_submitted'] = true;
    }

    $sql = "SELECT * FROM post WHERE user_id = '$user_id'";
    $result = $con->query($sql);
}

?>

<style>
    .post-box {
        margin-top: 200px;
    }
</style>

<div class="container post-box">
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="fullName">Your Name:</label>
            <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter Full Name" required>
        </div>
        <div class="form-group">
            <label for="fatherName">Product Name:</label>
            <input type="text" class="form-control" id="productName" name="productName" placeholder="Enter Product Name" required>
        </div>
        <div class="form-group">
            <label for="price">Selling Price:</label>
            <input type="text" class="form-control" id="price" name="price" placeholder="Enter Price" required>
        </div>
        <div class="form-group">
            <label for="message">Product Detail:</label>
            <textarea class="form-control" name="detail" id="detail" rows="5" placeholder="Enter your detail"></textarea>
        </div>
        <div class="form-group">
            <label for="select1">Select Categories:</label><br>
            <select class="form-control" id="select1" name="select1" required>
                <?php
                $res = mysqli_query($con, "select id,categories from categories order by categories asc");
                while ($row = mysqli_fetch_assoc($res)) {
                    if ($row['id'] == $categories_id) {
                        echo "<option selected value=" . $row['id'] . ">" . $row['categories'] . "</option>";
                    } else {
                        echo "<option value=" . $row['id'] . ">" . $row['categories'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="phoneNumber">Contact Number:</label>
            <input type="text" class="form-control" id="phoneNumber" maxlength="11" name="phoneNumber" placeholder="Enter Contact Number" required>
        </div>

        <div class="form-group">
            <label for="email">Address:</label>
            <input type="text" class="form-control" maxlength="30" id="address" name="address" placeholder="Enter Address" required>
        </div>
        <?php for ($i = 1; $i <= 5; $i++) { ?>
            <div class="form-group">
                <label for="fileToUpload<?= $i ?>">Upload Image <?= $i ?>:</label>
                <input type="file" class="form-control-file" id="fileToUpload<?= $i ?>" name="fileToUpload<?= $i ?>" accept="image/*">
            </div>
        <?php } ?>
        <button type="submit" class="btn btn-warning mt-3 mb-5">Submit</button>
    </form>
</div>

<?php require('footer.php'); ?>