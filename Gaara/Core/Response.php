<?php

declare(strict_types = 1);
namespace Gaara\Core;

use Closure;
use PhpConsole;
use Gaara\Core\Conf;
use Gaara\Core\Response\Traits\{
	SetTrait, GetTrait, Format, RequestInfo
};
use Gaara\Core\Response\Component\{
	Header, Body, File
};

/**
 * 处理系统全部响应( 输出 )
 */
class Response {

	use SetTrait,
	 GetTrait,
	 Format,
	 RequestInfo;

	// 响应状态码
	public $status;
	private $header;
	private $body;
	private $file;

	public function __construct() {
		$this->header	 = new Header;
		$this->file		 = new File;
		$this->body		 = new Body;
		$this->setContentType($this->getAcceptType())->setStatus(200)
		->header()->set('Pragma', 'no-cache')
		->add('Cache-Control', 'no-store')
		->add('Cache-Control', 'no-revalidate')
		->add('Cache-Control', 'no-cache')
		->set('Expires', gmdate("D, d M Y H:i:s", time() + 3600) . " GMT")
		->set('X-Powered-By', 'Gaara');
	}

	/**
	 *
	 * @return Header
	 */
	public function header(): Header {
		return $this->header;
	}

	/**
	 *
	 * @return Body
	 */
	public function body(): Body {
		return $this->body;
	}

	/**
	 *
	 * @return File
	 */
	public function file(): File {
		return $this->file;
	}

	/**
	 * 响应内容
	 * 不会清除最后层缓冲区, 也就不会出现响应头重复的问题
	 * 本方法易用性高, 建议业务中正常响应时使用
	 * @return void
	 */
	public function send(): void {
		$this->obRestore(function() {
			$this->header()->send();
			$this->body()->send();
		}, 1);
	}

	/**
	 * 响应内容
	 * 本方法将会即时发送全部响应头
	 * @return void
	 */
	public function sendReal(): void {
		$this->obRestore(function() {
			$this->header()->send();
			$this->body()->send();
		}, 0);
	}

	/**
	 * 响应内容
	 * 终止进程并发送全部响应头, 抛弃所有其他缓冲区的内容
	 * @return void
	 */
	public function sendExit(): void {
		$this->obRestore(function() {
			$this->header()->send();
			$this->body()->send();
		}, 0, false);
		exit;
	}

	/**
	 * 输出并还原缓冲区
	 * @param Closure $Closure 输出 (echo)
	 * @param int $leastLevel 输出时剩余的缓冲层
	 * @param bool $restore 是否还原其他输出
	 * @return void
	 */
	private function obRestore(Closure $Closure, int $leastLevel = 0, bool $restore = true): void {
		$output		 = [];
		$MaxLevel	 = ob_get_level();
		for ($i = $leastLevel; $i < $MaxLevel; $i++) {
			$output[] = $restore ? ob_get_contents() : '';
			ob_end_clean();
		}
		$Closure();
		for ($i = $leastLevel; $i < $MaxLevel; $i++) {
			ob_start();
			echo array_pop($output);
		}
	}

	/**
	 * 返回页面
	 * @param string $file
	 * @return Response
	 */
	public function view(string $file): Response {
		$data = obj(Template::class)->view($file);
		return $this->setContentType('html')->setContent($data);
	}

}
