<?php
if(isset($_POST['imagebase64'])){
        $data = $_POST['imagebase64'];

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        file_put_contents("images/uploaded/attachment/thumbs/".$_POST['url'], $data);
        echo "OK";
    }
   