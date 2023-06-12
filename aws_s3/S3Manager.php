<?php
    require $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/../inc/s3info.inc";

    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;

    class S3Manager
    {
        private $connect_params;
        private $S3;
        private $S3_bucket;

        public function __construct() {
            $this -> connect_params = array('region'=>REGION,'version'=> VERSION,'credentials' => array(
                'key' => ACCESS_KEY_ID,
                'secret' => SECRET_ACCESS_KEY,
                'signature' => 'v4'
              ));
            
            $this -> S3 = new S3Client($this->connect_params);
            $this -> S3_bucket = BUCKET;
        }

        public function uploadSingle($key,$path){
            $result = $this->S3 ->putObject([
                'Bucket' => $this->S3_bucket,
                'Key' => $key,
                'SourceFile' => $path,
                'ACL' => 'public-read'
        
              ]);
        
              $url = $result->get('ObjectURL');
        
              return $url;
        }

        public function uploadMulti($key_pre,$file_arr){
            $url_arr = array();
            $tmp_arr = array();
            foreach($file_arr as $file){
                $key = $key_pre.$file['name'];
                $path = $file['tmp_name'];

                $tmp = $key." ".$path;
                $tmp_arr[]=$tmp;
                
                $result = $this -> S3 -> putObject([
                    'Bucket' => $this -> S3_bucket,
                    'Key' => $key,
                    'SourceFile' => $file['tmp_name'],
                    'ACL' => 'public-read'
                ]);

                $url_arr[] = $result -> get('ObjectURL');

            }
            
            return $url_arr;
            // return $tmp_arr;
        }

        public function deleteSingle($key){
           
                $result = $this -> S3 -> deleteObject([
                    'Bucket' => $this->S3_bucket,
                    'Key' => $key
    
                ]);
        }

        public function deleteMulti($keys){
            foreach($keys as $key){
                
                $result = $this -> S3 -> deleteObject([
                    'Bucket' => $this->S3_bucket,
                    'Key' => $key
    
                ]);

            }
        }

        public function reArrayFiles($file_post){
            
            $file_ary = array();
            $file_count = count($file_post['name']);
            $file_keys = array_keys($file_post);
    
            for ($i=0; $i<$file_count; $i++) {
                foreach ($file_keys as $key) {
                    $file_ary[$i][$key] = $file_post[$key][$i];
                }
            }
    
            return $file_ary;
                }
    }

?>
