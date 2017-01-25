<?php

namespace protocol\window;

use Gui\Application;
use Gui\Components\Canvas;
use sf\Thread;

class QRCodeWindow extends Thread{
	private $image;
	private $needUpdate;
	private $closed;

	public function __construct($image){
		$this->image = $image;
		$this->start();
	}

	public function setQRCode($image){
		$this->image = $image;
		$this->needUpdate = true;
	}

	public function close(){
		$this->closed = true;
	}

	public function run(){
		$this->registerClassLoader();
		$this->needUpdate = false;
		$this->closed = false;
		$application = new Application([
			"title" => "请扫描二维码",
			"width" => 165,
			"height" => 165
		]);
		$application->on("start", function () use ($application){
			/** @var Canvas $canvas */
			$canvas = (new Canvas())->setTop(0)->setWidth(165)->setHeight(165)->setLeft(0);
			$canvas->setSize(165, 165)->setBackgroundColor("#ffffff");
			$image = imagecreatefromstring($this->image);
			for($x = 0; $x < imagesx($image); $x++){
				for($y = 0; $y < imagesy($image); $y++){
					$rgb = imagecolorat($image, $x, $y);
					$canvas->setPixel($x, $y, $rgb == 0 ? "#000000" : "#ffffff");
				}
			}

			$application->getLoop()->addPeriodicTimer(0.1, function () use ($canvas, $application) {
				if($this->needUpdate){
					$this->needUpdate = false;
					$image = imagecreatefromstring($this->image);
					for($x = 0; $x < imagesx($image); $x++){
						for($y = 0; $y < imagesy($image); $y++){
							$rgb = imagecolorat($image, $x, $y);
							$canvas->setPixel($x, $y, $rgb == 0 ? "#000000" : "#ffffff");
						}
					}
				};
				if($this->closed){
					$application->terminate();
				}
			});
		});
		$application->run();
	}
}