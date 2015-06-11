<?php

class UploadController extends AppController {
	public $uses = array();

	public function beforeFilter() {
		$this->autoRender = false;
	}

	public function video($id = null) {
	
		$this->loadModel('Help');
		
		if (!$id) {
			$path = 'uploads' . DS . 'tmp';
			$url = 'uploads/tmp/';
		} else {
			$video = $this->Help->findById($id);
			$path = 'uploads' . DS . 'videos';
			$url = 'uploads/videos/';
		}
		
		$this->_prepareDir($path);
		$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
		
		App::import('Vendor', 'qqFileUploader');
		$uploader = new qqFileUploader($allowedExtensions);
		
		// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
		$result = $uploader->handleUpload($path);
		
		if (!empty($result['success'])) {
			// resize image
			App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
			
			$photo = PhpThumbFactory::create($path . DS . $result['filename'], array('jpegQuality' => 100));
			$this->_rotateImage($photo, $path . DS . $result['filename']);
			
			$photo->resize(VIDEO_AVATAR_WIDTH, VIDEO_AVATAR_HEIGHT)->save($path . DS . $result['filename']);
			
			$photo = PhpThumbFactory::create($path . DS . $result['filename']);
			$photo->adaptiveResize(THUMB_WIDTH, THUMB_HEIGHT)->save($path . DS . 't_' . $result['filename']);
			
			if ($id) {
				// save to db
				$this->Help->id = $id;
				$this->Help->set(array('photo' => $result['filename']));
				$this->Help->save();
				
				// delete old files
				if ($video['Help']['photo'] && file_exists($path . DS . $video['Help']['photo'])) {
					unlink($path . DS . $video['Help']['photo']);
					unlink($path . DS . 't_' . $video['Help']['photo']);
				}
			}
			
			$result['avatar'] = $this->request->webroot . $url . $result['filename'];
			$result['filename'] = $result['filename'];
		}
		
		// to pass data through iframe you will need to encode all html tags
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}

	public function _getExtension($filename = null) {
		$tmp = explode('.', $filename);
		$re = array_pop($tmp);
		return $re;
	}
	
	private function _prepareDir($path) {
		$path = WWW_ROOT . $path;
		if (!file_exists($path)) {
			mkdir($path, 0755, true);
			file_put_contents($path . DS . 'index.html', '');
		}
	}
	
	private function _rotateImage(&$photo, $path) { 
		// rotate image if necessary
		$exif = exif_read_data($path);
		
		if (!empty($exif['Orientation'])) {
			switch ($exif['Orientation']) {
				case 8:
					$photo->rotateImageNDegrees(90)->save($path);
					break;
				case 3:
					$photo->rotateImageNDegrees(180)->save($path);
					break;
				case 6:
					$photo->rotateImageNDegrees(-90)->save($path);
					break;
			}
		}
	}
}

?>
