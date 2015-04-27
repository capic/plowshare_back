<?php
if($_POST){
    if($_POST['_METHOD'] == 'PUT'){
        echo post_to_url("http://192.168.1.200/api/download/".$_POST['id'], $_POST);
    }
    if($_POST['_METHOD'] == 'DELETE'){
        echo post_to_url("http://192.168.1.200/api/download/".$_POST['id'], $_POST);
    }
    if($_POST['_METHOD'] == 'POST'){
        echo post_to_url("http://192.168.1.200/api/download/", $_POST);
    }
} else{
 ?>
ADD RECORD.
<form action="" method="post">
<input type="text" name="name" placeholder="Name" /><br>
<input type="text" name="link" placeholder="Link" /><br>
<input type="hidden" name="_METHOD" value="POST" />
<input type="submit" value="A D D" />
</form>
<br>
UPDATE RECORD.
<br>
<form action="" method="post">
<input type="text" name="id" placeholder="Id to update" /><br>
<input type="text" name="name" placeholder="Name" /><br>
<input type="text" name="link" placeholder="Link" /><br>
<input type="text" name="status" placeholder="Status" /><br>
<input type="hidden" name="_METHOD" value="PUT" />
<input type="submit" value="U P D A T E" />
</form>
<br>
DELETE RECORD.
<br>
<form action="" method="post">
<input type="text" name="id" placeholder="Id to delete" /><br>
<input type="hidden" name="_METHOD" value="DELETE" />
<input type="submit" value="D E L E T E" />
</form>
 <?php 
}

function post_to_url($url, $data) {
   $fields = '';
   foreach($data as $key => $value) { 
      $fields .= $key . '=' . $value . '&'; 
   }
   rtrim($fields, '&');
   $post = curl_init();

   curl_setopt($post, CURLOPT_URL, $url);
   curl_setopt($post, CURLOPT_POST, count($data));
   curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
   curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

   $result = curl_exec($post);

   curl_close($post);
   return $result;
}
?>