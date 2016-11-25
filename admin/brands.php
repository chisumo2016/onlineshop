<?php
require_once '../core/init.php';
include "includes/head.php";
include "includes/nav.php";
//Get brands from database
$sql = "SELECT * FROM brands ORDER BY brand";
$result =$db->query($sql);
$errors = array();

//Delete Brands from the database
if(isset($_GET['delete']) && !empty($_GET['delete'])){
    $delete_id = (int)$_GET['delete'];
    $delete_id =  sanitize($delete_id);
    $sql_delete = "DELETE FROM brands WHERE id = '$delete_id'";
    $db->query($sql_delete);
    //Redirect page
    header('Location: brands.php');
    //echo $delete_id;
}
//Edit Brands in the database
if(isset($_GET['edit']) && !empty($_GET['edit'])){
    $edit_id =   (int)$_GET['edit'];
    $edit_id =   sanitize($edit_id);
    $sql_edit = "SELECT * FROM brands WHERE id = '$edit_id'";
    $edit_result = $db->query( $sql_edit);
    $edit_brand  = mysqli_fetch_assoc($edit_result);
}

// If add form is submitted
if(isset($_POST['add_submit'])){
    $brand  = sanitize($_POST['brand']);
    //check if brand is blank
    if($_POST['brand']== ''){
        $errors[] .= 'You must enter a brand!';

    }
    // Check if the brand exist in database
   $sql = "SELECT * FROM brands WHERE Brand = '$brand'";
    if(isset($_GET['edit'])){
        $sql = "SELECT * FROM brands WHERE Brand = '$brand' AND id != $edit_id";
    }
    $result = $db->query($sql);
    $count = mysqli_num_rows($result );  //echo $count;
    if($count > 0){
        $errors []  .=$brand .'The brand already exists.Pleas Choose another brand name....';
    }

    //Display error
    if(!empty($errors)){
         echo display_errors($errors);
    }else{
     // Add brand to database
            $sql = "INSERT INTO brands (Brand) VALUES ('$brand')";
            //Update
            if(isset($_GET['EDIT'])){
                $sql_update= "UPDATE brands SET Brand = '$brand' WHERE id = '$edit_id'";
            }
            $db->query($sql);
            header('Location: brands.php');
    }
}

?>
<h2 class="text-center">Brands</h2> <hr>
<!--Brand Form-->
 <div class="text-center">
     <form class="form-inline" action="brands.php<?=((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post">
         <div class="form-group">
             <?php
                    $brand_value = '';
                     if(isset($_GET['edit'])){
                          $brand_value =  $edit_brand['Brand'];
                     }else{
                        if(isset($_POST['brand'])){
                            $brand_value = sanitize($_POST['brand']);
                        }
                     }
             ?>
             <label for="brand"><?=((isset($_GET['edit']))?'Edit': 'Add A ');  ?>   Brand :</label>
             <input type="text" name="brand" id="brand" class="form-control" value="<?= $brand_value;?>">
             <?php if(isset($_GET['edit'])):  ?>

                 <a href="brands.php" class="btn btn-default">Cancel</a>
             <?php endif; ?>
             <input type="submit" name="add_submit" value="<?= ((isset($_GET['edit']))?'Edit':'Add') ;?> Brand" class="btn  btn-success">
         </div>

     </form>
 </div><hr>


<table class="table table-bordered table-striped table-auto table-condensed">
    <thead>
    <tr>
        <th></th><th>Brand</th><th></th>
    </tr>
    </thead>
    <tbody>
    <?php while($brand = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><a href="brands.php?edit=<?=$brand['id'] ;?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a></td>
            <td><?= $brand['Brand'];?></td>
            <td><a href="brands.php?delete=<?=$brand['id'] ;?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a></td>
        </tr>
    <?php endwhile;?>
    </tbody>
</table>

<?php include "includes/footer.php";?>
