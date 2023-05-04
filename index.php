<?php
    //creating connection
    $conn = mysqli_connect('localhost','root','','stdinfo');
    // //checking connection
    // if($conn){
    //     echo "Connection Established";
    // }
    session_start();

    //if click on button take filed value & insert to db
    if(isset($_POST['btn'])){
      //finding input filed value into variable
      $title = $_POST['title'];
      $content = $_POST['content'];
      $sentTo = $_POST['sentTo'];
        
      //if title & content field not empty perform insert operation
      if(!empty($title) && !empty($content) && !empty($sentTo)){
        //sql query // title string that's why keeping like string/text
        $query = "INSERT INTO student(title,content,sentTo,postdate,likes) VALUE('$title','$content','$sentTo',NOW(),0)";
        
        //sentToing data to database
        $createQuery = mysqli_query($conn, $query);
        if($createQuery){
          $_SESSION['success_message'] = "Data successfully inserted.";
          header("Location: index.php");
          exit();
        }
      }
      else{
        $_SESSION['error_message'] = "Field Should not be empty";
        header("Location: index.php");
        exit();
      }
    }
?>

<!-- code for delete  -->
<?php

  if (isset($_POST['likes'])) {
    // get the item ID from the form
    $itemId = $_POST['likes'];
    
    // update the like count in the database
    $sql = "UPDATE student SET likes = likes + 1 WHERE id = '$itemId'";
    mysqli_query($conn, $sql);
    
    // redirect the user back to the original page
    header("Location: index.php");
    exit();
}
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nootes++</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css">
  </head>
  <body>
    
    <nav class="navbar bg-primary" data-bs-theme="dark">
      <div class="container">
        <span class="navbar-brand mb-0 h1">Nootes++</span>
      </div>
    </nav>

    <div class="container text-center px-4">
      <div class="row gx-5">
        <div class="col-12 col-lg-4">
        
          <div class="p-3" style="position: sticky; top: 0">
            <div class="card sticky" >
              <img src="assets/images/image1.png" class="card-img-top img-fluid" alt="...">
              <div class="card-body">

              <?php if (isset($_SESSION['error_message'])) : ?>
                <div class="alert alert-danger" role="alert">
                  <?php echo $_SESSION['error_message']; ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
              <?php endif; ?>

              <form method="post" id="myForm">

                <div class="form-floating mb-3">
                  <input type="text" name="sentTo" class="form-control" id="floatingInput" placeholder="name@example.com">
                  <label for="floatingInput">Send to</label>
                </div>

                <div class="form-floating mb-3">
                  <input type="text" name="title" class="form-control" id="floatingInput" placeholder="name@example.com">
                  <label for="floatingInput">Title</label>
                </div>

                <div class="mb-3 form-floating">
                  <textarea class="form-control" name="content" placeholder="Leave a comment here" id="floatingTextarea" style="height: 150px"></textarea>
                  <label for="floatingTextarea">Content</label>
                </div>

                <button class="btn btn-primary me-md-2 float-end" type="submit" name="btn"><i class="bi bi-plus-lg"></i> Add</button>
              </form>

              </div>
            </div>
          </div>

        </div>
        <div class="col-12 col-lg-8" >

        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-absolute alert-success custom-alert" id="myDiv" role="alert">
              <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
          <?php endif; ?>

          <form class="d-flex mt-3" method="POST">
            <input class="form-control me-2" type="search" placeholder="Search your name" aria-label="Search" name="search">
            <button class="btn btn-outline-success" type="submit">Search</button>
            <?php if(isset($_POST['search']) && !empty($_POST['search'])) { ?>
              <a href="?search="><button type="button" class="btn btn-outline-secondary ms-2">Clear</button></a>
            <?php } ?>
          </form>

          <div class="p-3 text-start main-container overflow-auto" style="max-height: 80vh">

            <div class="container px-4 text-start">
              <div class="row gx-1">

              <?php
                 
                if (isset($_POST['search'])) {
                  $search_query = $_POST['search'];
                  $search_query = mysqli_real_escape_string($conn, $search_query);
                  $query = "SELECT * FROM student WHERE sentTo LIKE '%$search_query%' ORDER BY likes DESC";
                  $count_query = "SELECT COUNT(*) as count FROM student WHERE sentTo LIKE '%$search_query%'";
                  $readQuery = mysqli_query($conn, $query);
                } else {
                    $search_query = '';
                    // set the number of results per page
                    $results_per_page = 40;

                    // get the current page number
                    if (isset($_GET['page'])) {
                      $page_number = $_GET['page'];
                    } else {
                      $page_number = 1;
                    }

                    // calculate the offset
                    $offset = ($page_number - 1) * $results_per_page;

                    $query = "SELECT * FROM student ORDER BY likes DESC LIMIT $results_per_page OFFSET $offset";
                    $count_query = "SELECT COUNT(*) as count FROM student";
                    
                    $readQuery = mysqli_query($conn, $query);

                    // count the total number of rows in the table
                    $count_result = mysqli_query($conn, $count_query);
                    $count_row = mysqli_fetch_assoc($count_result);
                    $total_results = $count_row['count'];

                    // calculate the total number of pages
                    $total_pages = ceil($total_results / $results_per_page);

                  }
        
                // loop through the data and display it
                if ($readQuery->num_rows > 0) {
                  while ($rd = mysqli_fetch_assoc($readQuery)) {
                    $stdid = $rd['id'];
                    $title = $rd['title'];
                    $content = $rd['content'];
                    $sentTo = $rd['sentTo'];
                    $postdate = $rd['postdate'];
                    $likes = $rd['likes'];
                ?>

                <?php
                $last_three_digits = substr($stdid, -3); // get the last three digits of the URL
                ?>

                <div class="col">
                  <div class="p-1">
                    <div class="card" style="width: 18rem; max-height: fit-content">
                      <div class="card-body" id="test<?php echo"$stdid"?>">
                        <form method="post" action="index.php#test<?php echo"$stdid"?>">
                          <h5 class="card-title"><?php echo"$title" ?></h5>
                          <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo"$postdate" ?></h6>
                          <hr>
                          <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                              <li class="breadcrumb-item active" aria-current="page">Letter</li>
                              <li class="breadcrumb-item" aria-current="page"><?php echo"$sentTo" ?></li>
                            </ol>
                          </nav>
                          <p class="card-text"><?php echo"$content" ?></p>
                          <div class="d-grid gap-2 d-md-flex justify-content-start">
                              <button type="submit" id="<?php echo"$stdid"?>" class="btn <?php echo ($_SERVER['REQUEST_URI'] == $_SERVER['SCRIPT_NAME'] . '#' . $last_three_digits) ? 'btn-danger' : ''; ?>" name="likes" value="<?php echo $stdid; ?>"><i class="bi bi-heart"></i> <?php echo $likes; ?></button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>

                <?php
                  }
                } else {
                  echo "No data to show";
                }

                // display the pagination links
                if (isset($total_pages) && $total_pages > 1) {
                  echo "<nav aria-label='Page navigation example'><ul class='pagination justify-content-center'>";
                  for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $page_number) {
                      echo "<li class='page-item active'><a class='page-link' href='#'>$i</a></li>";
                    } else {
                      echo "<li class='page-item'><a class='page-link' href='?page=$i'>$i</a></li>";
                    }
                  }
                  echo "</ul></nav>";
                }

                ?>
                

  
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <script>

      // Get the div element
      const myDiv = document.getElementById("myDiv");

      // Set a timeout to hide the div after 5 seconds (5000 milliseconds)
      setTimeout(function() {
        myDiv.style.display = "none";
      }, 5000);

      var url = window.location.href; // get the full URL including the anchor (hash) part using JavaScript
      console.log(url)
      var lastThreeDigits = url.substr(-3); // get the last three digits of the URL
      console.log(lastThreeDigits)
      var btn = document.getElementById(lastThreeDigits);
        btn.classList.add("btn-danger"); // if the condition is true, add the 'btn-danger' class to the button (red color)
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  </body>
</html>