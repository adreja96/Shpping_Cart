<?php

class shopping_cart
{
    private $conn;

    public function __construct()
    {
        // Database host, Database user, Database Pass, Database Name

        $dbhost = 'localhost';
        $dbuser = 'root';
        $dbpass = "";
        $dbname = 'shop_db';

        $this->conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

        if (!$this->conn) {
            die("Database Connection Error!!");
        }
    }

    public function admin_login($data)
    {
        $admin_email = $data['admin_email'];
        $admin_pass = md5($data['admin_pass']);

        $query = "SELECT * FROM admin_info WHERE admin_email='$admin_email' && admin_pass='$admin_pass'";

        if (mysqli_query($this->conn, $query)) {
            $admin_info = mysqli_query($this->conn, $query);

            if ($admin_info) {
                header("location: dashboard.php");
                $admin_data = mysqli_fetch_assoc($admin_info);
                session_start();
                $_SESSION['admin_id'] = $admin_data['id'];
                $_SESSION['admin_name'] = $admin_data['admin_name'];
            }
        }
    }

    public function adminLogout()
    {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        header('location:index.php');
    }



    public function add_product($data)
    {
        $name = $data['name'];
        $price = $data['price'];
        $image = $_FILES['image']['name'];
        $img_tmp = $_FILES['image']['tmp_name'];

        $query = "INSERT INTO products(name, price, image) VALUES('$name', '$price', '$image')";

        if (mysqli_query($this->conn, $query)) {
            move_uploaded_file($img_tmp, '../images/' . $image);
            return "Product Added Successfully";
        }
    }

    public function display_products()
    {
        $query = "SELECT * FROM products";

        if (mysqli_query($this->conn, $query)) {
            $posts = mysqli_query($this->conn, $query);
            return $posts;
        }
    }
    public function delete_product($id)
    {
        $query = "DELETE FROM products WHERE id=$id";

        if (mysqli_query($this->conn, $query)) {
            return "Product Deleted Seccessfully";
        }
    }
}
