<?php

namespace OCA\DAV\Upload;

use Sabre\DAV\IFile;

class AssemblyStream implements \Icewind\Streams\File {

	/** @var resource */
	private $context;

	/** @var IFile[] */
	private $nodes;

	/** @var int */
	private $pos = 0;

	/** @var array */
	private $sortedNodes;

	/** @var int */
	private $size;

	/**
	 * @param string $path
	 * @param string $mode
	 * @param int $options
	 * @param string &$opened_path
	 * @return bool
	 */
	public function stream_open($path, $mode, $options, &$opened_path) {
		$this->loadContext('assembly');

		// sort the nodes
		$nodes = $this->nodes;
		@usort($nodes, function(IFile $a, IFile $b) {
			return strcmp($a->getName(), $b->getName());
		});
		$this->nodes = $nodes;

		// build additional information
		$this->sortedNodes = [];
		$start = 0;
		foreach($this->nodes as $node) {
			$size = $node->getSize();
			$name = $node->getName();
			$this->sortedNodes[$name] = ['node' => $node, 'start' => $start, 'end' => $start + $size];
			$start += $size;
			$this->size = $start;
		}
		return true;
	}

	/**
	 * @param string $offset
	 * @param int $whence
	 * @return bool
	 */
	public function stream_seek($offset, $whence = SEEK_SET) {
		return false;
	}

	/**
	 * @return int
	 */
	public function stream_tell() {
		return $this->pos;
	}

	/**
	 * @param int $count
	 * @return string
	 */
	public function stream_read($count) {

		list($node, $posInNode) = $this->getNodeForPosition($this->pos);
		if (is_null($node)) {
			return null;
		}
		$stream = $this->getStream($node);

		fseek($stream, $posInNode);
		$data = fread($stream, $count);
		$read = strlen($data);

		// update position
		$this->pos += $read;
		return $data;
	}

	/**
	 * @param string $data
	 * @return int
	 */
	public function stream_write($data) {
		return false;
	}

	/**
	 * @param int $option
	 * @param int $arg1
	 * @param int $arg2
	 * @return bool
	 */
	public function stream_set_option($option, $arg1, $arg2) {
		return false;
	}

	/**
	 * @param int $size
	 * @return bool
	 */
	public function stream_truncate($size) {
		return false;
	}

	/**
	 * @return array
	 */
	public function stream_stat() {
		return [];
	}

	/**
	 * @param int $operation
	 * @return bool
	 */
	public function stream_lock($operation) {
		return false;
	}

	/**
	 * @return bool
	 */
	public function stream_flush() {
		return false;
	}

	/**
	 * @return bool
	 */
	public function stream_eof() {
		return $this->pos >= $this->size;
	}

	/**
	 * @return bool
	 */
	public function stream_close() {
	}


	/**
	 * Load the source from the stream context and return the context options
	 *
	 * @param string $name
	 * @return array
	 * @throws \Exception
	 */
	protected function loadContext($name) {
		$context = stream_context_get_options($this->context);
		if (isset($context[$name])) {
			$context = $context[$name];
		} else {
			throw new \BadMethodCallException('Invalid context, "' . $name . '" options not set');
		}
		if (isset($context['nodes']) and is_array($context['nodes'])) {
			$this->nodes = $context['nodes'];
		} else {
			throw new \BadMethodCallException('Invalid context, nodes not set');
		}
		return $context;
	}

	/**
	 * @param IFile[] $nodes
	 * @return resource
	 *
	 * @throws \BadMethodCallException
	 */
	public static function wrap(array $nodes) {
		$context = stream_context_create([
			'assembly' => [
				'nodes' => $nodes]
		]);
		stream_wrapper_register('assembly', '\OCA\DAV\Upload\AssemblyStream');
		try {
			$wrapped = fopen('assembly://', 'r', null, $context);
		} catch (\BadMethodCallException $e) {
			stream_wrapper_unregister('assembly');
			throw $e;
		}
		stream_wrapper_unregister('assembly');
		return $wrapped;
	}

	/**
	 * @param $pos
	 * @return IFile | null
	 */
	private function getNodeForPosition($pos) {
		foreach($this->sortedNodes as $node) {
			if ($pos >= $node['start'] && $pos < $node['end']) {
				return [$node['node'], $pos - $node['start']];
			}
		}
		return null;
	}

	/**
	 * @param IFile $node
	 * @return mixed
	 */
	private function getStream(IFile $node) {
		$data = $node->get();
		if (is_resource($data)) {
			return $data;
		}

		return fopen('data://text/plain,' . $data,'r');
	}

}
