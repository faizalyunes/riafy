<?php
include "include/layout/header.php";
?>


<div class="container mt-5">
    <div class="text-center">
        <h1>Welcome, <?php echo $_SESSION["user"]['name']; ?>!</h1>
        <p class="lead">Explore movies and manage your favourites.</p>
    </div>
</div>
</body>
</html>
