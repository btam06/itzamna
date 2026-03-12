<?php

namespace Itzamna;

use Carbon\Carbon;
use Exception;
use InvalidArgumentException;

/**
 * @copyright  Copyright (c) 2018 Avery Tam
 * @author     Avery Tam [bt] <btam06@gmail.com>
 * @license    MIT
 */
class Ics
{
	/**
	 * Default timezone
	 * @var string
	 */
	public const DEFAULT_TIMEZONE = 'UTC';

	/**
	 *
	 */
	public const TEMPLATE_DIRECTORY = 'Templates';


	/**
	 * [protected description]
	 * @var [type]
	 */
	protected $events = array();


	/**
	 * [protected description]
	 * @var [type]
	 */
	protected $prodid;

	/**
	 *
	 */
	protected $textFormatter;

	/**
	 *
	 */
	protected $dateFormatter;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->textFormatter = new TextFormatter();
		$this->dateFormatter = new DateFormatter();
	}


	/**
	 * [addEvent description]
	 * @param EventInterface $event [description]
	 */
	public function addEvent(EventInterface $event)
	{
		$this->events[$event->getICSUid()] = $event;
		return $this;
	}


	/**
	 * [getEvents description]
	 * @return [type] [description]
	 */
	public function getEvents()
	{
		return $this->events;
	}


	/**
	 * Get the TextFormatter instance
	 * @return TextFormatter
	 */
	public function getTextFormatter()
	{
		return $this->textFormatter;
	}


	/**
	 * [setProdid description]
	 * @param [type] $prodid [description]
	 */
	public function setICSProdid($prodid)
	{
		$this->prodid = strip_tags($prodid);
		return $this;
	}

	/**
	 * [getProdid description]
	 * @return [type] [description]
	 */
	public function getProdid()
	{
		return $this->prodid;
	}

	/**
	 * Output the ICS
	 *
	 * @return string The ICS file as a string
	 */
	public function make()
	{
		$timestamp = new Carbon();

		extract([
			'events'     => $this->getEvents(),
			'prodid'     => $this->getProdid(),
			'formatText' => $this->textFormatter,
			'formatDate' => $this->dateFormatter
		]);

		ob_start('trim');
		include_once($this->getTemplate('Ics.php'));
		$output = ob_get_clean();

		if (php_sapi_name() == 'cli') {
			$out = str_replace("\r\n", PHP_EOL, $output);
		}

		return $output;
	}


	/**
	 *
	 */
	protected function getTemplate($template)
	{
		$path = realpath(__DIR__ . '/../' . self::TEMPLATE_DIRECTORY . '/' . $template);

		if (file_exists($path)) {
			return $path;
		}

		throw new Exception(sprintf('ICS template could not be found at %s', realpath($path)));
	}

	/**
	 * [padTimezoneOffset description]
	 * @param [type] $gmt_offset [description]
	 */
	private function padTimezoneOffset($gmt_offset)
	{
		$gmt_offset  = number_format(floatval($gmt_offset), 2, '', '');
		$gmt_offset  = strval($gmt_offset);
		$sign        = strpos($gmt_offset, '-') === 0 ? '-' : '';
		$gmt_offset  = str_replace('-', '', $gmt_offset);
		$gmt_offset  = str_pad($gmt_offset, 4, '0', STR_PAD_LEFT);
		$gmt_offset  = $sign . $gmt_offset;
		return $gmt_offset;
	}
}
