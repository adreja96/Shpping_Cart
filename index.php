<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
};

if (isset($_GET['logout'])) {
   unset($user_id);
   session_destroy();
   header('location:login.php');
};

if (isset($_POST['add_to_cart'])) {

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if (mysqli_num_rows($select_cart) > 0) {
      $message[] = 'product already added to cart!';
   } else {
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');
      $message[] = 'product added to cart!';
   }
};

if (isset($_POST['update_cart'])) {
   $update_quantity = $_POST['cart_quantity'];
   $update_id = $_POST['cart_id'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
   $message[] = 'cart quantity updated successfully!';
}

if (isset($_GET['remove'])) {
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
   header('location:index.php');
}

if (isset($_GET['delete_all'])) {
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:index.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shopping cart</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- CSS only -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">

</head>

<body>

   <?php
   $select_user = mysqli_query($conn, "SELECT * FROM `user_info` WHERE id = '$user_id'") or die('query failed');
   if (mysqli_num_rows($select_user) > 0) {
      $fetch_user = mysqli_fetch_assoc($select_user);
   };
   ?>

   <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container py-1">
         <a class="navbar-brand" href="index.php">SHOPPING_CART</a>
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav">
               <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                     <?php echo $fetch_user['name']; ?>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end p-3" style="width: 400px">
                     <li>Email: <b><?php echo $fetch_user['email']; ?></b></li>
                     <?php if (!isset($user_id)) { ?>
                        <li><a href="login.php" class="btn">login</a></li>
                        <li><a href="register.php" class="option-btn">register</a></li>
                     <?php } ?>
                     <li><a href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('are your sure you want to logout?');" class="btn btn-danger">logout</a></li>
                  </ul>
               </li>
            </ul>
         </div>
      </div>
   </nav>



   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
      }
   }
   ?>

   <div class="container mt-5">


      <div class="products">

         <h1 class="heading">latest products</h1>

         <div class="box-container">

            <?php
            $select_product = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
            if (mysqli_num_rows($select_product) > 0) {
               while ($fetch_product = mysqli_fetch_assoc($select_product)) {
            ?>
                  <form method="post" class="box" action="">
                     <img src="images/<?php echo $fetch_product['image']; ?>" alt="">
                     <div class="name"><?php echo $fetch_product['name']; ?></div>
                     <div class="price">$<?php echo $fetch_product['price']; ?>/-</div>
                     <input type="number" min="1" name="product_quantity" value="1">
                     <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                     <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                     <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                     <input type="submit" value="add to cart" name="add_to_cart" class="btn btn-primary">
                  </form>
            <?php
               };
            };
            ?>

         </div>

      </div>

      <div class="shopping-cart mt-5">

         <h1 class="heading">shopping cart</h1>

         <table>
            <thead>
               <th>image</th>
               <th>name</th>
               <th>price</th>
               <th>quantity</th>
               <th>total price</th>
               <th>action</th>
            </thead>
            <tbody>
               <?php
               $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               $grand_total = 0;
               if (mysqli_num_rows($cart_query) > 0) {
                  while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
               ?>
                     <tr>
                        <td><img src="images/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
                        <td><?php echo $fetch_cart['name']; ?></td>
                        <td>$<?php echo $fetch_cart['price']; ?>/-</td>
                        <td>
                           <form action="" method="post">
                              <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                              <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                              <input type="submit" name="update_cart" value="update" class="btn btn-warning">
                           </form>
                        </td>
                        <td>$<?php echo $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</td>
                        <td><a href="index.php?remove=<?php echo $fetch_cart['id']; ?>" class="btn btn-danger" onclick="return confirm('remove item from cart?');">remove</a></td>
                     </tr>
               <?php
                     $grand_total += $sub_total;
                  }
               } else {
                  echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">no item added</td></tr>';
               }
               ?>
               <tr class="table-bottom">
                  <td colspan="4">grand total :</td>
                  <td>$<?php echo $grand_total; ?>/-</td>
                  <td><a href="index.php?delete_all" onclick="return confirm('delete all from cart?');" class="btn btn-danger <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">delete all</a></td>
               </tr>
            </tbody>
         </table>

         <div class="cart-btn">
            <a href="#" class="btn btn-primary <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">proceed to checkout</a>
         </div>

      </div>

   </div>

   <!-- JavaScript Bundle with Popper -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

</body>

</html>