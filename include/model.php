<?php
/**
* DBハンドルを取得
* @return obj $link DBハンドル
*/
function get_db_connect() {
    // コネクション取得
    if (!$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME)) {
        die('error: ' . mysqli_connect_error());
    }
    // 文字コードセット
    mysqli_set_charset($link, DB_CHARACTER_SET);
    return $link;
}
/**
 * DB goods追加 
 * @param $newName,$newPrice,$newStock,$newStatus,$newImage_adress
 * 
 * @return 
 */
function db_goods_add($link,$newName,$newPrice,$newStock,$newStatus,$newImage_adress){
    $error = [];
    $time = date('Y-m-d H:i:s');
    mysqli_autocommit($link,false);
    //info_table INSERT
    $sql = "INSERT INTO 
           ec_goods_item_table(goods_name,goods_price,goods_img,goods_status,created_date,updated_date)
           VALUES('{$newName}',{$newPrice},'{$newImage_adress}',{$newStatus},'{$time}','{$time}')";
    $result = mysqli_query($link,$sql);
    if($result === false){
        $error[] = 'info_tableに追加失敗しました。';
    }
    //get drink ID
    $goods_id = mysqli_insert_id($link);
    //stock_table INSERT
    $sql="INSERT INTO 
         ec_goods_stock_table(goods_id,stock,created_date,updated_date)
         VALUES ({$goods_id},{$newStock},'{$time}','{$time}')";
    $result = mysqli_query($link,$sql);
    if($result === false){
        $error[] = 'stock_tableに追加失敗しました。';
    }
    if(count($error) == 0){
        mysqli_commit($link);
    }else{
        mysqli_rollback($link);
    }
    return $error;
}
/**
 * DB　表　updete
*/
function db_stock_update($link,$changed_stock,$change_goods_id){
    $error = [];
    $time = get_time();
    $sql = "UPDATE ec_goods_stock_table 
            SET stock={$changed_stock},updated_date='{$time}' 
            WHERE goods_id={$change_goods_id}";
    if(mysqli_query($link,$sql) === false){
        $error[] = 'stockの更新か失敗しました。';
    }
    return $error;
}
function db_status_update($link,$change_status,$change_goods_id){
    $error = [];
    $time = get_time();
    $sql = "UPDATE ec_goods_item_table 
            SET goods_status={$change_status},updated_date = '{$time}' 
            WHERE goods_id={$change_goods_id}";
    if(mysqli_query($link,$sql) === false){
        $error[] = 'statusの更新か失敗しました。';
    }
    return $error;
}
function db_goods_delete($link,$change_goods_id){
    $error = [];
    $sql = "DELETE FROM ec_goods_item_table
            WHERE goods_id = {$change_goods_id}";
    if(mysqli_query($link,$sql) === false){
        $error[] = 'Delete か失敗しました。';
    }
    return $error;
}

/**
 * DB goods検索 goodsデータ
 * @param object $link
 * @return Array data[]
 */
function get_goods_table_array($link){
    $data = [];
    //在庫表示SQL
    $sql = 'SELECT 
    ec_goods_item_table.goods_id,ec_goods_item_table.goods_img,ec_goods_item_table.goods_name,
    ec_goods_item_table.goods_price,ec_goods_item_table.goods_status,
    ec_goods_stock_table.stock
    FROM ec_goods_item_table 
    JOIN ec_goods_stock_table ON ec_goods_stock_table.goods_id=ec_goods_item_table.goods_id';
    return get_as_array($link,$sql);
}

/**
 * DB user検索 userデータ
 * @param object $link
 * @return Array data[]
 */
function get_user_table_array($link){
    $data = [];
    //User SQL
    $sql = 'SELECT user_id,user_name,created_date,updated_date
            FROM ec_user_table';
    return get_as_array($link,$sql);
}
/**
 * get DB top page goods_date
 * @param object $link
 * @return Array data[]
 */
function get_topPage_goods_table_array($link){
    $data = [];
    //User SQL
    $sql = 'SELECT ec_goods_item_table.goods_id,ec_goods_item_table.goods_name,
    ec_goods_item_table.goods_price,ec_goods_item_table.goods_img,ec_goods_item_table.goods_status,ec_goods_stock_table.stock
    FROM ec_goods_item_table
    JOIN ec_goods_stock_table
    ON ec_goods_stock_table.goods_id=ec_goods_item_table.goods_id
    WHERE ec_goods_item_table.goods_status =1';
    return get_as_array($link,$sql);
}
/**
* クエリを実行しその結果を配列で取得する
*
* @param obj  $link DBハンドル
* @param str  $sql SQL文
* @return array 結果配列データ
*/
function get_as_array($link, $sql) {
 
    // 返却用配列
    $data = [];
 
    // クエリを実行する
    if ($result = mysqli_query($link, $sql)) {
 
        if (mysqli_num_rows($result) > 0) {
 
            // １件ずつ取り出す
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
 
        }
 
        // 結果セットを開放
        mysqli_free_result($result);
 
    }
 
    return $data;
 
}
/**
 * DB LINK CLOSE
 * @param object $link
 */
function close_db_connect($link){
    mysqli_close($link);
}

/**
 * DB Selsect 結果取得
 * @param object $link
 * @param String &sql
 * @return Array data[]
 */
// function select_db_as_array($link,$sql){
//     $data=[];
//     if($result = mysqli_query($link,$sql)){
//         //クエリ結果有無判断
//         if(mysqli_num_rows($result) > 0){
//             while($row = mysqli_fetch_assoc($result)){
//                 $data[] = $row;
//             }
//         }
//         mysqli_free_result($result);
//     }
// }

/**
 * DB inset & update 実行
 * @param object $link
 * @param String &sql
 * @return boolean true||false
 */
function query_db($link,$sql){
    return mysqli_query($link,$sql);
}

/**
* リクエストメソッドを取得
* @return str GET/POST/PUTなど
*/
function get_request_method() {
   return $_SERVER['REQUEST_METHOD'];
}

/**
* POSTデータを取得
* @param str $key 配列キー
* @return str　不为空 POST値
*               空     “”
*/
function get_post_data($key) {
   $str = '';
   if (isset($_POST[$key]) === TRUE) {
       $str = $_POST[$key];
   }
   return $str;
}

/**
* Image type、存在チェック
* @param  str  $name  Input file Name
* @param  str  $img_dir  Image保存位置
* @return no error --> str $newImage_adress
*         error    --> array $error
* 判断is_array() $error->true ;$newImage_adress->false
*/
function img_checksave($name,$img_dir){
    $phpFileUploadErrors = array(
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
    );
    $error = [];
    $allowedExts = ['jpeg','png'];
    $temp = explode(".", $_FILES[$name]['name']);
    $extension = end($temp);
    if($_FILES[$name]['size'] == 0){
        $error[]='ファイルを選択してください。';
    }else{
        //ファイルtype確認
        if(in_array($extension,$allowedExts) === false){
            $error[]='ファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';
        }
        if($_FILES[$name]['error'] > 0){
            //Upload 失敗error指定
            $error[] = 'Image file Upload error:'.$phpFileUploadErrors[$_FILES[$name]['error']];
        }
    }
    
    if(count($error) === 0){
        // 保存用ユーニック画像 Name 生成
        $image_save_name = md5(uniqid(mt_rand(),true)).'.'.$extension;
        // 今drink_Image/下にファイルは存在するかどうか判断する
        if(file_exists($img_dir.$image_save_name)){
            $error[]='もう一度アップロードしてください';
        }else{
        // ファイル保存
            move_uploaded_file($_FILES[$name]['tmp_name'],$img_dir.$image_save_name);
            //ファイル保存アドレス"drink_Image/" . $image_save_name
            $newImage_adress = $img_dir.$image_save_name;
            return $newImage_adress;
        }
    }else{
        return $error;
    }
}

 
/**
* 特殊文字をHTMLエンティティに変換する
* @param str  $str 変換前文字
* @return str 変換後文字
*/
function entity_str($str) {
    return htmlspecialchars($str, ENT_QUOTES, HTML_CHARACTER_SET);
}

/**
* PHP log print
* @param str  $name  log名前
* @param str  $str log内容
*/
function log_write_str($name,$str){
    $time = date('h:i:s');
    $fp = fopen('./bug_log.txt', 'a');
    fwrite($fp,$time.'/*'.$name.'*/'.$str."\n");
    fclose($fp);
}
/**
* PHP log print
* @param str  $name  log名前
* @param str  $array arraylog内容
*/
function log_write_array($name,$array){
    $time = date('h:i:s');
    $fp = fopen('./bug_log.txt', 'a');
    $i = 0;
    if(isset($array)){
        if(count($array)===0){
            fwrite($fp,$time.'/*'.$name.'*/ は 空'."\n");
        }else{
            if(count($array) == count($array,COUNT_RECURSIVE)){
                foreach($array as $value){
                    fwrite($fp,$time.'/*'.$name.'*/['.$i.']:'.$value."\n");
                    $i++;
                }
            }else{
                foreach($array as $key=>$value){
                    if(count($value)===0){
                        fwrite($fp,$time.'/*'.$name.'*/'.'['.$key.'] is 空'."\n");
                    }else{
                        $i = 0;
                        foreach($value as $v){
                            fwrite($fp,$time.'/*'.$name.'*/'.'['.$key.']'.'['.$i.']:'.$v."\n");
                            $i++;
                        }
                    }
                }
            }
        }
    }else{
        fwrite($fp,$time.'/*'.$name.'*/ is NULL'."\n");
    }
    fclose($fp);
}

/**
 * Add DATA CHECK
 * @return array $error
 */
function add_data_check($newName,$newPrice,$newStock,$newStatus){
    $error = [];
    if($newName == ''){
        array_push($error,'名前を入力してください。');
    }
    
    if($newPrice == ''){
        array_push($error,'値段を入力してください。');
    }else{
        if(preg_match(NUM_REGEXP,$newPrice) !== 1){
            array_push($error,'値段は半角数字を入力してください。');
        }else{
            $newPrice = intval($newPrice);
        }
        if($newPrice < 0){
            array_push($error,'値段は0以上の整数を入力してください。');
        }
    }
    
    if($newStock == ''){
        array_push($error,'個数を入力してください。');
    }else{
        
        if(preg_match(NUM_REGEXP,$newStock) !== 1){
            array_push($error,'個数は半角数字を入力してください。');
        }else{
            $newStock = intval($newStock);
        }
        if($newStock < 0){
            array_push($error,'個数は0以上の整数を入力してください。');
        }
    }
    
    if($newStatus == 0){
        array_push($error,'公開or非公開選択下さい。');
    }
    return $error;
}
/**
 * change stock DATA CHECK
 * @return array $error
*/
function stock_check($changed_stock,$change_goods_id){
    $error = [];
    if($change_goods_id == ''){
        array_push($error,'get POST goods_id error');
    }
    if($changed_stock == ''){
        $error[]='在庫数入力下さい。';
    }
    if(preg_match(NUM_REGEXP,$changed_stock) !== 1){
        $error[]= '個数は半角数字を入力してください。';
    }else{
        $changed_stock = intval($changed_stock);
    }
    if($changed_stock < 0){
        array_push($error,'個数は0以上の整数を入力してください。');
    }
    return $error;
}
/**
 * change status DATA CHECK
 * @return array $error
*/
function status_check($change_status,$change_goods_id){
    $error = [];
    if($change_goods_id == ''){
        array_push($error,'get POST goods_id error');
    }
    if($change_status == ''){
        array_push($error,'get $change_status error');
    }
    return $error;
}

function get_time(){
    return date('Y-m-d H:i:s');
}
/**
 * DB user_id チェック
 * @param int  $user_id $_SESSION['user_id']
 * return str $user_name
 *        true  DB username
 *        false  空str''
*/
function user_id_check($user_id){
    //userid check
    //DB link
    $user_name = "";
    $link = get_db_connect();
    $sql = "SELECT user_name
            FROM ec_user_table
            WHERE user_id ={$user_id}";
    $data = get_as_array($link,$sql);
    if(isset($data[0]['user_name'])){
        $user_name = $data[0]['user_name'];
    }
    //DB Close
    close_db_connect($link);
    return $user_name;
}

/**
 * 同じユーザー名が既に登録されているがチェク
 * @param str  $new_user_name
 * @return  true || false
**/
function new_user_name_db_check($new_user_name){
    //DB link
    $data = [];
    $link = get_db_connect();
    $sql = "SELECT user_id FROM ec_user_table
            WHERE user_name = '{$new_user_name}'";
    $data = get_as_array($link,$sql);
     //DB Close
    close_db_connect($link);
    if(isset($data[0]['user_id'])){
        return false;
    }else{
        return true;
    }
}


/**
 * DBインセント
 * @param str $sql
 * @return  true || false
**/
function db_insert($sql){
    $link = get_db_connect();
    $result = query_db($link,$sql);
    close_db_connect($link);
    return $result;
}
function db_update($sql){
    $link = get_db_connect();
    $result = query_db($link,$sql);
    close_db_connect($link);
    return $result;
}

/**
 * change input ammont DATA CHECK
 * @return array $error
*/
function amount_check($select_amount){
    $error = [];
    if($select_amount == ''){
        $error[]='在庫数入力下さい。';
    }
    if(preg_match(NUM_REGEXP,$select_amount) !== 1){
        $error[]= '個数は半角数字を入力してください。';
    }else{
        $select_amount = intval($select_amount);
    }
    if($select_amount <= 0){
        array_push($error,'数量は１以上の整数を入力してください。');
    }
    return $error;
}
/**
 * priceFormat
 * 价格格式处理
*/
function priceFormat($price){
    return number_format($price);
}

/**
 * print関数
**/
function print_data($data){
    var_dump($data);
    exit;
}