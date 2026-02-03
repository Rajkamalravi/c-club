<?php

if(isset($_GET['ops']) && $_GET['ops'] == 'delete_cache'){
    $dir = TAOH_PLUGIN_PATH.'/cache/general/';
    $files = scandir($dir);
    $mod = $_GET['mod'];
    foreach($files as $file){
        if(is_file($dir.$file) && strpos($file, $mod.'_') === 0){
            unlink($dir.$file);
        }
    }
}
taoh_get_header();
?>
<script>
let cache_delete_mod = '<?php echo $_GET['mod'];  ?>';
var db_name = cache_delete_mod+'_intao';
getIntaoDb(db_name).then((db) => {
  const dataStoreName = objStores.data_store.name;
  const transaction = db.transaction(dataStoreName, 'readwrite');
  const objectStore = transaction.objectStore(dataStoreName);
  const request = objectStore.openCursor();
  request.onsuccess = (event) => {
    const cursor = event.target.result;
    console.log(cursor);
    if (cursor) {
      const index_key = cursor.primaryKey;
      if(
        index_key.includes(cache_delete_mod)
      ){
        objectStore.delete(index_key);
      }
      cursor.continue();
    }
  };
  window.location.href="<?php echo TAOH_SITE_URL_ROOT.'/';?>"+cache_delete_mod+'/dash?cache_deleted=1';
  
});
/* getIntaoDb().then((db) => {
  const dataStoreName = objStores.data_store.name;
  const transaction = db.transaction(dataStoreName, 'readwrite');
  const objectStore = transaction.objectStore(dataStoreName);
  const request = objectStore.openCursor();
  request.onsuccess = (event) => {
    const cursor = event.target.result;
    if (cursor) {
      const index_key = cursor.primaryKey;
      alert(index_key);
      if(
        index_key.includes(cache_delete)
      ){
        objectStore.delete(index_key);
      }
      cursor.continue();
    }
  };
}).catch((err) => {
  console.log('Error in deleting data store');
}); */
</script>
<?php taoh_get_footer(); ?>