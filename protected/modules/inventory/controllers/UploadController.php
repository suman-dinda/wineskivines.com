<?php
class UploadController extends CController
{
	public $code=2;
	public $msg;
	public $details;
	public $data;
	
	public function __construct()
	{
		$this->data=$_POST;	
		
		FunctionsV3::handleLanguage();
	    $lang=Yii::app()->language;	    	   
	    if(isset($_GET['debug'])){
	       dump($lang);
	    }
	}
	
	public function beforeAction($action)
	{
		if(!UserWrapper::validToken()){
			return false;
		}
		
		return true;
	}
	
	private function jsonResponse()
	{		
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
	}
	
	public function actionSingle()
	{		
		require_once('SimpleUploader.php');
		$path_to_upload  = FunctionsV3::uploadPath();
		$valid_extensions = FunctionsV3::validImageExtension();        
		$Upload = new FileUpload('uploadfile');
		$ext = $Upload->getExtension();
		
		
		if(!in_array($ext,$valid_extensions)){
			$this->msg = translate("Invalid file extension");
			$this->jsonResponse();
		}
		
		$time=time();
        $filename = $Upload->getFileNameWithoutExt();       
        $filename = str_replace(" ",'-',$filename);
        $new_filename =  "$time-$filename.$ext";        
        $Upload->newFileName = $new_filename;
        $Upload->sizeLimit = FunctionsV3::imageLimitSize();
        $result = $Upload->handleUpload($path_to_upload, $valid_extensions); 
        if (!$result) {
        	$this->msg=$Upload->getErrorMsg();
        } else {
        	$this->code = 1;
	    	 $this->msg="OK";
	    	 $this->details=array(
	    	   'file_name'=>$new_filename,
	    	   'file_url'=>websiteUrl()."/upload/$new_filename",	    	   
	    	 );
        }		
		$this->jsonResponse();
	}
	
	public function actionRemove()
	{		
		$filename = isset($this->data['filename'])?$this->data['filename']:'';
		if(!empty($filename)){
			$path_to_upload  = FunctionsV3::uploadPath();
			$file_path = "$path_to_upload/$filename";
			if(file_exists($file_path)){
				@unlink($file_path);
				$this->code = 1;
				$this->msg = "OK";					
			} else $this->msg = translate("file not found");
		} else $this->msg = translate("file not found");
		$this->details = array('next_action'=>"silent");
		$this->jsonResponse();
	}
	
	public function actionGet()
	{
		
        header('Content-type: text/json');  
        header('Content-type: application/json');
        
		$this->data = $_GET;
		$filename = isset($this->data['filename'])?$this->data['filename']:'';
		if(!empty($filename)){
			$path_to_upload  = FunctionsV3::uploadPath();
			$file_path = "$path_to_upload/$filename";
			if(file_exists($file_path)){
				$this->code = 1;
				$this->msg = "OK";	
				$this->details = array(
				  'name'=>$filename,
				  'size'=>filesize($file_path),
				  'link'=>FunctionsV3::getImage($filename),
				  'classname'=>str_replace(".","",$filename)
				);
			} else $this->msg = translate("file not found");
		} else $this->msg = translate("filename is empty");
		$this->jsonResponse();
	}
	
}
/*end class*/