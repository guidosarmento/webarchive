<?php

// connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'webarchive');
$sql = "SELECT * FROM tbl_uploads";
$result = mysqli_query($conn, $sql);
//ADD MORE FUNCTION FOR CONNECTING TO TBL_CATEGORY
$sql = "SELECT * FROM tbl_category";

		// DECLARAR VARIABEL DA TABELE tbl_uploads
$id=0;
$update = false;
$name = '';
$ref_no = '';
$author = '';
$descript = '';
$name = '';
$CatId= '';
//$CatName '';


		// ATÉ AQUI

$tbl_uploads = mysqli_fetch_all($result, MYSQLI_ASSOC);

//Add halo hanesan ho iha leten ba tbl_category nian
$tbl_category = mysqli_fetch_all($result, MYSQLI_ASSOC);


// ====================================UPLOAD FILES====================================
	
	if (isset($_POST['save'])) { // if save button on the form is clicked
    // name of the uploaded file
    $filename = $_FILES['myfile']['name'];
	
// AUMENTA TAN INFORMASAUN KONA BA FILE UPLOADED  - HILI CATEGORIA CARTA
//INNER JOIN TABLE-UPLOADS HO TABLE_CATEGORY
			//"SELECT tbl_uploads.id, tbl_uploads.ref_no, tbl_uploads.author, tbl_uploads.descript, CatId 
			//FROM tbl_uploads INNER JOIN tbl_category ON tbl_uploads.CatId=tbl_category.CatId WHERE tbl_uploads.id=tbl_category.CatId";
			//"SELECT tbl_uploads.id AS tbl_uploads, tbl_category.CatId AS tbl_category FROM tbl_uploads, tbl_category";
			
	$sql ="SELECT tbl_uploads.id, tbl_uploads.name, tbl_uploads.author, tbl_uploads.ref_no, tbl_uploads.descript, tbl_category.CatId, tbl_category.CatName
	FROM tbl_uploads, tbl_category WHERE tbl_uploads.id = tbl_category.CatId ORDER BY tbl_uploads.id";
	
	$result = mysqli_query($conn, $sql);

	// AUMENTA TAN INFORMASAUN KONA BA FILE UPLOAD
	$ref_no = $_POST['ref_no'];
	$author = $_POST['author'];
	$descript = $_POST['descript'];
	
	//Add tan iha nee ba category nian
	//$CatId = $_POST['CatId'];
	$CatName= $_POST['CatName'];
	
    // destination of the file on the server
    $destination = 'uploads/' .$filename;

    // get the file extension
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    // the physical file on a temporary uploads directory on the server
    $file = $_FILES['myfile']['tmp_name'];
    $size = $_FILES['myfile']['size'];
	
//AUMENTA TAN EXTENSAUN PPTX ATU BELE UPLOAD POWERPOINT SLIDES
    if (!in_array($extension, ['zip', 'pdf', 'docx', 'pptx'])) {
	// file must be in extension that has been stated
        echo "<script> alert('You file extension must be .zip, .pdf or .docx and .pptx')</script>";
			} elseif ($_FILES['myfile']['size'] > 1000000) { 
	// file shouldn't be larger than 1Megabyte
        echo "<script> alert('Your Upload File is Too Large!')</script>";
			} else {
        // move the uploaded (temporary) file to the specified destination
        if (move_uploaded_file($file, $destination)) {
            $sql = "INSERT INTO tbl_uploads (name, size, downloads, ref_no, author, descript, CatId) VALUES ('$filename', $size, 0, '$ref_no', '$author', '$descript', '$_POST[CatName]')";
            if (mysqli_query($conn, $sql)) {
				// NOT YET WORKING FOR SOME OF THE COMMANDS BELLOW --
				echo "<script> alert('Your File Is Uploaded Sucessfully.')</script>";
				header('location: archivefiles.php');
				}
			} else {
            echo "<script> alert('Failed To Upload File.')</script>";
			header('location: uploads.php');
        }
    }

}


//INNER JOIN TABLE-UPLOADS HO TABLE_CATEGORY

			
	// ====================================DOWNLOAD FILES====================================
	if (isset($_GET['file_id'])) {
    $id = $_GET['file_id'];

    // fetch file to download from database
    $sql = "SELECT * FROM tbl_uploads WHERE id=$id";
    $result = mysqli_query($conn, $sql);

    $file = mysqli_fetch_assoc($result);
    $filepath = 'uploads/' . $file['name'];

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize('uploads/' . $file['name']));
        readfile('uploads/' . $file['name']);

        // Now update downloads count
        $newCount = $file['downloads']+1;
        $updateQuery = "UPDATE tbl_uploads SET downloads=$newCount WHERE id=$id";
        mysqli_query($conn, $updateQuery);
        exit;
    }
}

// DELETE  UPLOADED FILES
if (isset($_GET['delete'])){
	$id = $_GET['delete'];
			//NEW CODE INPUT - TROKA IHA NE'E TONA
	$sql = "DELETE FROM tbl_uploads WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
	
	//AUMENTA TAN IHA NEE
	// NOT YET WORKING FOR SOME OF THE COMMANDS BELLOW --
	echo "<script> alert('Your File Is Deleted Sucessfully.')</script>";
	header("location: archivefiles.php");
}

		
	//KRIA FUNSAUN EDITAR DADUS
	if (isset($_GET['edit'])){
	$id = $_GET['edit'];
	$update = true;
	
	$sql="SELECT * FROM tbl_uploads WHERE id = '$id'";	
	$result = mysqli_query($conn, $sql);
		
		if (count($result)==1){
		$file = $result->fetch_array();
		$CatId = $file['CatId'];
		$ref_no = $file['ref_no'];
		$author = $file['author'];
		$descript = $file['descript'];
		$name = $file['name'];
		
		}
	}
		
	if (isset($_POST['update'])){
	$id = $_POST['id'];
	$CatId = $_POST['CatId'];
	$ref_no = $_POST['ref_no'];
	$author = $_POST['author'];
	$descript = $_POST['descript'];
	
$sql="UPDATE tbl_uploads SET ref_no ='$ref_no', author ='$author', descript ='$descript'  WHERE id ='$id'";
// NOT YET WORKING FOR SOME OF THE COMMANDS BELLOW --
	if (mysqli_query($conn, $sql)) {
		  echo "<script>alert('Record updated successfully')</script>";
	   } else {
		  echo "<script>alert('Error to update Record')</script>";
	   }
	   mysqli_close($conn);
	
header("location: archivefiles.php");
	}
?>







