<?php

require_once('spreadutils.php');

?>

<html>
<body>

<form method="post" action="upload.php" enctype="multipart/form-data">
    <p>
        <input type="file" name="testfile" />
    </p>
    <p>
        <input type="submit" name="upload" value="Submit" />
    </p>
</form>

<?php

$link = mysql_connect('localhost', 'spread', 'password');
if (!$link) {
   die('Could not connect to database: ' . mysql_error());
}
mysql_select_db('spreaddb') or die('Could not select database');

if (isset($_POST['upload']) && $_POST['upload'] == 'Submit') {
        
    $fname = $_FILES['testfile']['tmp_name'];
    $name = $_FILES['testfile']['name'];
    if (is_uploaded_file($fname)) {
        $id = spread_connect('4804', 'phptest');
        if ($id == null) {
            die('Could not connect to spread daemon');
        }
        
        $file = file_get_contents($fname);
        mysql_query("insert into uploaded_files (name) values ('$name')") or die('Update failed: ' . mysql_error());
        $f_id = mysql_insert_id();

        $msg = new Message();
        $msg->set_header('f_id', $f_id);
        $msg->set_content($file);
       
        spread_multicast('uploadfiles', $msg->str());
        spread_disconnect($id);
    }
}

$query = 'select * from uploaded_files';
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

echo '<table border="1">
    <tr>
        <th>ID</th>
        <th>NAME</th>
        <th>SIZE</th>
        <th>NUM LINES</th>
    </tr>';

while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    echo '<tr>';

    foreach ($line as $col) {
        echo '<td>' . $col . '</td>';
    }
    
    echo '</tr>';
}
echo '</table>';

mysql_close($link);
?>

</body>
</html>
