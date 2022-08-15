<?php
if (isset($_GET['token'])) {
    require 'JwtHandler.php';
    $jwt = new JwtHandler();

    $data =  $jwt->jwtDecodeData(trim($_GET['token']));

    if(isset($data->id) && isset($data->name) && isset($data->email)):
        echo "<ul>
        <li>ID => $data->id</li>
        <li>Name => $data->name</li>
        <li>Email => $data->email</li>
        </ul>";
    else:
        print_r($data);
    endif;
}
?>
<form action="" method="GET">
    <label for="_token"><strong>Enter Token</strong></label>
    <input type="text" name="token" id="_token">
    <input type="submit" value="Docode">
</form>