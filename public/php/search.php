<?php

$connection = mysql_connect('localhost','root','') or die(mysql_error());
$database = mysql_select_db('csgoPlay') or die(mysql_error());

if($_POST)
{
    $q=$_POST['search'];
    $sql_res=mysql_query("select id,user from usuario");
    while($row=mysql_fetch_array($sql_res))
    {
        $username=$row['user'];
        $email=$row['id'];
        $b_username='<strong>'.$q.'</strong>';
        $b_email='<strong>'.$q.'</strong>';
        $final_username = str_ireplace($q, $b_username, $username);
        $final_email = str_ireplace($q, $b_email, $email);
        ?>
        <div class="show" align="left">
            <img src="author.PNG" style="width:50px; height:50px; float:left; margin-right:6px;" /><span class="name"><?php echo $final_username; ?></span>&nbsp;<br/><?php echo $final_email; ?><br/>
        </div>
    <?php
    }
}
?>