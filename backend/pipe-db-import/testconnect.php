<?php


$db = pg_connect("host=10.189.9.148 port=5432 dbname=free_the_lots user=postgres password=");

if($db){
  echo 'Connected';

}
else{
  echo 'Failed';
}
