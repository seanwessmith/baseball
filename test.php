<?php
//testing caching parts of code on server
$memcache = new Memcache;
$memcache->connect('localhost', 11211);
function slowAndHeavyOperation() {
    sleep(1);
    return date('d/m/Y H:i:s');
}
$item1 = $memcache->get('item');
if ($item1 === false) {
    $item1 = slowAndHeavyOperation();
    $memcache->set('item', $item1);
}
echo $item1;
function slowAndHeavyOperation() {
    sleep(1);
    return date('d/m/Y H:i:s');
}
$item1 = slowAndHeavyOperation();
echo $item1;
?>
