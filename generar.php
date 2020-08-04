<?php


if(isset($_POST['base64']) && !empty($_POST['base64'])){

    $img = $_POST['base64'];
    $img = str_replace('data:image/png;base64,', '', $img);
    $fileData = base64_decode($img);
    $fileName = uniqid().'.png';
    file_put_contents($fileName, $fileData);
    
    
    header('Content-type: image/png');
    
    header('Content-Disposition: attachment; filename="img.png"');
    
    readfile($fileName);

    unlink($fileName);

}else if(isset($_POST['base65']) && !empty($_POST['base65'])){

    $img = $_POST['base65'];
    $img = str_replace('data:image/png;base64,', '', $img);
    $fileData = base64_decode($img);
    $fileName = uniqid().'.png';
    file_put_contents($fileName, $fileData);
    
    
    header('Content-type: image/png');
    
    header('Content-Disposition: attachment; filename="img.png"');
    
    readfile($fileName);

    unlink($fileName);

}else if(isset($_POST['base66']) && !empty($_POST['base66'])){

    $img = $_POST['base66'];
    $img = str_replace('data:image/png;base64,', '', $img);
    $fileData = base64_decode($img);
    $fileName = uniqid().'.png';
    file_put_contents($fileName, $fileData);
    
    
    header('Content-type: image/png');
    
    header('Content-Disposition: attachment; filename="img.png"');
    
    readfile($fileName);

    unlink($fileName);

}else if(isset($_POST['base67']) && !empty($_POST['base67'])){

    $img = $_POST['base67'];
    $img = str_replace('data:image/png;base64,', '', $img);
    $fileData = base64_decode($img);
    $fileName = uniqid().'.png';
    file_put_contents($fileName, $fileData);
    
    
    header('Content-type: image/png');
    
    header('Content-Disposition: attachment; filename="img.png"');
    
    readfile($fileName);

    //unlink($fileName);

}

?>