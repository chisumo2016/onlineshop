<?php
require_once  $_SERVER['DOCUMENT_ROOT'].'/core/init.php';
include  'includes/head.php';
include  'includes/nav.php';
if(isset($_GET['add'])){
    $brandQuery = $db->query("SELECT * FROM brands ORDER  BY Brand");
    $parentQuery = $db->query("SELECT * FROM categories WHERE parent  = 0 ORDER  BY category");
    //$izesArray = array();
    if($_POST){
        $title     = sanitize($_POST['title']);
        $price=sanitize($_POST['price']);
        $list_price=sanitize($_POST['list_price']);
        $brand      = sanitize($_POST['brand']);
        $categories  =sanitize($_POST['child']);
        $dbpath = '';
        $description=sanitize($_POST['description']);
        $sizes=sanitize($_POST['sizes']);

        $errors = array();
        if (!empty($_POST['sizes'])){
            $sizeString = sanitize($_POST['sizes']);
            $sizeString = rtrim($sizeString, ','); //echo $sizeString;
            $sizeArray = explode(',', $sizeString);
            $sArray = array();
            $qArray = array();
            foreach ($sizeArray as $ss ){
                $s = explode(':', $ss);
                $sArray[] = $s[0];
                $qArray[] = $s[1]; // we can use this inside our modal
            }
        }else{$sizeArray = array();}

        // Validation form
        $required = array('title', 'brand','price','parent','child','sizes');
        foreach ($required as $field){
            if($_POST[$field] == ''){
                $errors[] = 'All fields with and Astrisk are required.';
                break;
            }
        }
        // Check our file
        if(!empty($_FILES)){
           var_dump($_FILES);
            $photo = $_FILES['photo'];
            $name = $photo['name'];
            $nameAArray  = explode('.', $name);
            $filename = $nameAArray[0];
            $fileExt = $nameAArray[1];
            $mime = explode('/',$photo['type']);
            $mimeType = $mime[0];
            $mimeExt  = $mime[1];
            $tmpLoc = $photo['tmp_name'];
            $fileSize = $photo['size'];
            $allowed = array('png', 'jpg','jpeg','gif');

            $uploadName = md5(microtime()).'.'.$fileExt;
            $uploadPath = BASEURL.'/images/products/'.$uploadName;
            $dbpath = '/images/products/'.$uploadName;

            //Validation  -photo
            if($mimeType != 'image'){
                $errors[] ='The file must be an image';
            }
            // Check for file extension
            if(!array($fileExt, $allowed)){
               $errors[]  = 'The file extension must be  a png, jpeg, jpg, or gif';
            }
            //Check for size
            if($fileSize > 1500000){
                  $errors[] ='The files size must be under 15MB';
            }
            //Check the file  validation ext
            if($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt !='jpeg')){
               $errors[]  = 'File extension does not match the file';
            }

        }
        if(!empty($errors)){
            echo display_errors($errors);
        }else{
            // Upload file and insert into database

            move_uploaded_file($tmpLoc,$uploadPath);

            $insertSql = "INSERT INTO products (  `title`,`price`,`list_price`,`brands`,`categories`,`image`,`description`,`sizes`)
                          VALUE('$title ', '$price','$list_price',' $brand ','$categories','$dbpath','$description','$sizes')";
            $db->query($insertSql);
            header('Location: products.php');
        }
    }

    ?>

    <h2 class="text-center">Add A NEW  PRODUCT</h2><hr>
    <form action="products.php?add=1" method="post" enctype="multipart/form-data">
        <div class="form-group col-md-3">
            <label for="title">Title* :</label>
            <input type="text" class="form-control" id="title" name="title">
        </div>

        <div class="form-group col-md-3">
            <label for="brand">Brand* :</label>
            <select class="form-control" id="brand" name="brand">
                <option value="<?=((isset($_POST['brand']) && $_POST['brand'] == '')?' selected':'');?>"></option>
                <?php while ($band = mysqli_fetch_assoc($brandQuery )) : ?>
                    <option value="<?=$band['id'];?>"<?=((isset($_POST['brand']) && $_POST['brand']==$band['id'])?' selected':'')?>><?=$band['Brand'];?></option>
                <?php endwhile; ?>
            </select>

        </div>
        <div class="form-group col-md-3">
            <label for="parent">Parent Category* :</label>
            <select  class="form-control" id="parent" name="parent">
                <option value=""<?=((isset($_POST['parent']) && $_POST['parent'] == '')?' selected': '')?>></option>
                <?php while ($parent = mysqli_fetch_assoc($parentQuery )) : ?>
                    <option value="<?=$parent['id'];?>"<?= ((isset($_POST['parent']) && $_POST['parent']== $parent['id'])?:' selected') ;?>><?=$parent['category'];?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group col-md-3" >
            <label for="child">Child Category* :</label>
            <select name="child" id="child" class="form-control"></select>
        </div>

        <div class="form-group col-md-3">
            <label for="price">Price* :</label>
            <input type="text" name="price" id="price" class="form-control" value="<?=((isset($_POST['price']))?sanitize($_POST['price']):' ');?>">
        </div>

        <div class="form-group col-md-3">
            <label for="price">List Price :</label>
            <input type="text" name="list_price" id="price" class="form-control" value="<?=((isset($_POST['list_price']))?sanitize($_POST['list_price']):' ')?>">
        </div>

        <div class="form-group col-md-3">
            <label for="">Quantity % Size* :</label>
            <button class="btn btn-default form-control" onclick="jQuery('#sizesModal').modal('toggle');return false;">Quantity & Sizes</button>
        </div>

        <div class="form-group col-md-3">
            <label for="sizes">Sizes & Qty Preview</label>
            <input type="text" class="form-control" name="sizes" id="sizes" value="<?=((isset($_POST['sizes']))?$_POST['sizes']:'');?>" readonly>
        </div>
        <div class="form-group col-md-6">
            <label for="photo">Product Photo* :</label>
            <input type="file" name="photo" id="photo" class="form-control">
        </div>
        <div class="form-group col-md-6">
            <label for="description">Description* :</label>
            <textarea type="text" id="description" name="description" class="form-control" rows="6"> <?=((isset($_POST['description']))?sanitize($_POST['description']):'');?></textarea>
        </div>
        <div class="form-group pull-right ">
            <input type="submit" value="Add Product" class="form-control btn btn-success pull-right">
        </div><div class="clearfix"></div>

    </form>

    <!-- Modal -->
    <div class="modal fade " id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="#sizesModal">Size $ Quantity</h4>
                </div>
                <div class="modal-body">
                    <div class="containe-fluid">
                    <?php for ($i=1; $i <= 12; $i++ ):?>
                    <div class="form-group col-md-4">
                        <label for="size<=?$i;?>">Size:</label>
                        <input type="text" name="size<?=$i;?>" id="size<?=$i;?>" value="<?=((!empty($sArray[$i-1]))?$sArray[$i-1]:'');?>" class="form-control">
                    </div>

                        <div class="form-group col-md-2">
                            <label for="qty<=?$i;?>">Quantity:</label>
                            <input type="number" name="qty<?=$i;?>" id="qty<?=$i;?>" value="<?=((!empty($qArray[$i-1]))?$qArray[$i-1]:'');?>" min="0" class="form-control">
                        </div>
                    <?php endfor;?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updateSizes();jQuery('#sizesModal').modal('toggle');return false;">Save changes</button>
                </div>
            </div>
        </div>
    </div>

<?php }else{
$sql = "SELECT * FROM products WHERE deleted = 0";
$product_result = $db->query($sql);
if (isset($_GET['featured'])){
    $id = (int)$_GET['id'];
    $featured =(int)$_GET['featured'];
    $featuredSql = "UPDATE products SET featured = '$featured' WHERE id ='$id ' ";
    $db->query($featuredSql);
    header('Location: products.php');
}
?>
<h2 class="text-center">Products</h2>
<a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Addd Products</a>
<div class="clearfix"></div>
<hr>
<table class="table table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th></th>
            <th>Product</th>
            <th>Price</th>
            <th>Categories</th>
            <th>Featured</th>
            <th>Sold</th>
        </tr>
    </thead>
    <tbody>
         <?php while($product = mysqli_fetch_assoc($product_result)) :
             // Set a category of child element
             $childID = $product['categories'];
             $category_sql = "SELECT * FROM categories WHERE id =$childID ";
             $result = $db->query($category_sql);
             $child = mysqli_fetch_assoc( $result);
             $parent_id =  $child['parent'];
             $parent_sql = "SELECT * FROM categories WHERE id = ' $parent_id'";
             $p_result=$db->query($parent_sql);
             $parent = mysqli_fetch_assoc($p_result);
             $category = $parent['category'].'~'.$child['category'];

             ?>
        <tr>
            <td>
                <a href="products.php?edit=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="products.php?delete=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>
            </td>
            <td><?=$product['title'];?></td>
            <td><?=money($product['price']);?></td>
            <td><?=$category;?></td>
            <td><a href="products.php?featured=<?=(($product['featured']==0 )?'1':'0');?>&id=<?=$product['id'];?>" class="btn btn-xs btn-default">
                    <span class="glyphicon glyphicon-<?=(($product['featured']==1)?'minus':'plus');?>"></span >

                </a> &nbsp <?=(($product['featured']==1)?'Featured Product': '');?></td>
            <td>0</td>
        </tr>
        <?php endwhile;?>
    </tbody>
</table>




<?php } include 'includes/footer.php';?>

