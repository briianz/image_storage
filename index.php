<?php
$file = 'gallery_data.txt';
$upload_dir = 'uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $image_path = "";

    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {

        $original_name = basename($_FILES["user_image"]["name"]);
        $file_type = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($file_type, $allowed_types)) {
            $new_filename = uniqid() . "." . $file_type;
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_path)) {
                $image_path = $target_path;
            }
        }
    }

    if (!empty($image_path)) {
        $data_package = [
            'timestamp' => date("Y-m-d H:i"),
            'image' => $image_path
        ];

        $entry = json_encode($data_package) . "\n";
        file_put_contents($file, $entry, FILE_APPEND);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Photo Gallery</title>
    <link rel="stylesheet" href="gallery.css">
</head>

<body>

    <h1>Gallery</h1>

    <form method="POST" action="" enctype="multipart/form-data">
        <label>Upload a Photo:</label><br><br>
        <input type="file" name="user_image" required>
        <button type="submit">Upload</button>
    </form>

    <div class="gallery">
        <?php
        if (file_exists($file)) {
            $lines = file($file);
            $lines = array_reverse($lines);

            foreach ($lines as $line) {
                $data = json_decode($line, true);

                if ($data && !empty($data['image'])) {
                    echo "<div class='photo-card'>";
                    echo "<img src='" . $data['image'] . "'>";
                    echo "<small>" . $data['timestamp'] . "</small>";
                    echo "</div>";
                }
            }
        }
        ?>
    </div>

</body>

</html>