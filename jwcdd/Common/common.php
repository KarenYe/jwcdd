<?php
  //open the session
  function setSession($sessionName,$username){
    session($sessionName,$username);
  }
  //检查是否设置session
  function checkSession($sName){
    if(session($sName) != null){
      return true;
    }else{
      return false;
    }
  }
  //check that administritor is log in or not
  function checkLogin(){
    if(session("?userName") && session("userName") != null){
      return true;
    }else{
      //header("location: http://219.224.30.99:8081/jwcdd/Login/login");
    }
    return true;
  }
  //upload image
  function uploadImage($imagePath,$thumbWidth,$thumbHeight){
      import("ORG.Net.UploadFile");
      $upload = new UploadFile(); //instantiate the class
      $upload->maxSize = 3145728;  //set the max size of image is 3M
      $upload->uploadReplace = true;  //replace the same name
      $upload->allowExts = array("jpg","gif","png","jpeg"); //set the type of image
      $upload->savePath = $imagePath; //set the path of image to save
      $upload->saveRule = uniqid; //the rule of image name
      $upload->thumb = true;  //open the thumb
      $upload->thumbMaxWidth = $thumbWidth;
      $upload->thumbMaxHeight = $thumbHeight;
      $upload->thumbPrefix = "s_,m_";
      $upload->thumbRemoveOrigin = 1; //delete the original image
      //var_dump($upload);
      if(!$upload->upload()){ //failure
        //var_dump($upload->getErrorMsg);
        Log::write("图片文件上传失败",ERR);
        return false;
      }else{  //success
        $info = $upload->getUploadFileInfo();
        Log::write("图片文件上传成功",INFO);
        return $info;
      }
    }

    //upload file
    function uploadExcelFile($filePath){
      import("ORG.Net.UploadFile");
      $upload = new UploadFile();
      $upload->maxSize = 3145728; //上传文件最大为3M
      $upload->uploadReplace = true;  //替换相同的文件
      $upload->savePath = $filePath;  //设置保存的路径
      $upload->saveRule = uniqid;
      $upload->allowExts = array("xls","xlsx"); //允许的文件后缀名
      if(!$upload->upload()){
        Log::write("文件上传失败",ERR);
        return false;
      }else{
        $info = $upload->getUploadFileInfo();
        Log::write("文件上传成功",ERR);
        return $info;
      }
    }

    function uploadDocFile($filePath){
      import("ORG.Net.UploadFile");
      $upload = new UploadFile();
      $upload->maxSize = 3145728; //上传文件最大为3M
      $upload->uploadReplace = true;  //替换相同的文件
      $upload->savePath = $filePath;  //设置保存的路径
      $upload->saveRule = '';
      $upload->allowExts = array("pdf"); //允许的文件后缀名
      if(!$upload->upload()){
        Log::write("文件上传失败",ERR);
        return false;
      }else{
        $info = $upload->getUploadFileInfo();
        Log::write("文件上传成功",ERR);
        return $info;
      }
    }

    /**
    *正则匹配，匹配指定的子字符串
    *@param String $pattern 正则表达式
    *@param String $string 匹配字符串
    */
    function patternMatch($pattern,$string){
      preg_match_all($pattern, $string, $result,PREG_SET_ORDER);
      return $result;
    }

    function i_array_column($input, $columnKey, $indexKey=null){
         if(!function_exists('array_column')){ 
             $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
             $indexKeyIsNull            = (is_null($indexKey))?true :false; 
             $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
             $result                         = array(); 
             foreach((array)$input as $key=>$row){ 
                 if($columnKeyIsNumber){ 
                     $tmp= array_slice($row, $columnKey, 1); 
                     $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
                 }else{ 
                     $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
                 } 
                 if(!$indexKeyIsNull){ 
                     if($indexKeyIsNumber){ 
                       $key = array_slice($row, $indexKey, 1); 
                       $key = (is_array($key) && !empty($key))?current($key):null; 
                       $key = is_null($key)?0:$key; 
                     }else{ 
                       $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                     } 
                 } 
                 $result[$key] = $tmp; 
             } 
             return $result; 
         }else{
             return array_column($input, $columnKey, $indexKey);
         }
     }
?>