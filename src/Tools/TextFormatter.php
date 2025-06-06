<?php
namespace Itzamna;

/**
 * 
 */
class TextFormatter
{
	protected array $translations = [
		"</p>"   => '.\n  ',
		"<br/>"  => '\n',
		"\r\n"   => '\n',
		"&nbsp;" => ' ',
		","      => '\,',
		";"      => '\;',
		":"      => '\:'
	];

	/**
	 * Add a translation to the formatter
	 *
	 * @param string $key The key to translate
	 * @param string $value The value to translate to
	 */
	public function addTranslation(string $key, string $value) 
	{
		$this->translations[$key] = $value;
	}

	public function getTranslations() 
	{
		return $this->translations;
	}

	/**
	 * Format HTML, strip tags, escape various characters for ICS text safety
	 *
	 * @param string $text The string to format
	 * @return string Formatted text
	 */
	public function __invoke($text) 
	{
		$text = str_replace(array_keys($this->translations), array_values($this->translations), $text);
		$text = strip_tags($text, '<a>');
		$text = html_entity_decode($text);

		return $text;
	}

}