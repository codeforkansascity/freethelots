<?php
$file = 'testdata';
$result = csv_to_array($file, '|');
$connection['host'] = "192.168.99.100";
$connection['port'] = "32770";
$connection['dbname'] = "random";
$connection['user'] = "root";
$connction['password'] = "secret";

var_dump($result);


function arrayToPostgres($arr){

  $db = pg_connect("host=192.168.99.100 port=32770 dbname=random user=root password=secret");
  $query = '';

  $insert = " INSERT INTO $table (";
  $columns = implode(array_keys($arr[0]), ',');
  $insert .= $columns.') ';

  foreach($arr as $row){
    $values = 'VALUES ( '. implode($row, ','). ');';
      $query .= $insert.' '.$values.' ';
  }
  echo $query;
  die;
  $ret = pg_query($db, $query);
   if(!$ret) {
      echo pg_last_error($db);
   } else {
      echo "Records created successfully\n". PHP_EOL;
   }
  $ret = pg_query($db, "SELECT * FROM company");
  $ret = pg_fetch_all($ret);
  var_dump($ret);

  pg_close($db);
}



// get csv file and convert to array
function csv_to_array($filename, $delimiter)
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;
    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 8000, $delimiter)) !== FALSE)
        {
            if(!$header){
                $header = $row;
              }
            else{
              if(count($header) != count($row)){
                echo "<br> header count == ".count($header) ."<br>";
                var_dump($header);
                echo "<br> row count == ".count($row) ."<br>";
                var_dump($row);
              }
              $data[] = array_combine($header, $row);
            }
        }
        fclose($handle);
    }
    return $data;
}
