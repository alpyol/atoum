<?php

namespace mageekguy\atoum\report\fields\runner\errors;

use
	\mageekguy\atoum,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\report,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\errors
{
	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $methodPrompt = null;
	protected $methodColorizer = null;
	protected $errorPrompt = null;
	protected $errorColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $methodPrompt = null, colorizer $methodColorizer = null, prompt $errorPrompt = null, colorizer $errorColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setTitlePrompt($titlePrompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setMethodPrompt($methodPrompt ?: new prompt())
			->setMethodColorizer($methodColorizer ?: new colorizer())
			->setErrorPrompt($errorPrompt ?: new prompt())
			->setErrorColorizer($errorColorizer ?: new colorizer())
		;
	}

	public function setTitlePrompt(prompt $prompt)
	{
		$this->titlePrompt = $prompt;

		return $this;
	}

	public function getTitlePrompt()
	{
		return $this->titlePrompt;
	}

	public function setTitleColorizer(colorizer $colorizer)
	{
		$this->titleColorizer = $colorizer;

		return $this;
	}

	public function getTitleColorizer()
	{
		return $this->titleColorizer;
	}

	public function setMethodPrompt(prompt $prompt)
	{
		$this->methodPrompt = $prompt;

		return $this;
	}

	public function getMethodPrompt()
	{
		return $this->methodPrompt;
	}

	public function setMethodColorizer(colorizer $colorizer)
	{
		$this->methodColorizer = $colorizer;

		return $this;
	}

	public function getMethodColorizer()
	{
		return $this->methodColorizer;
	}

	public function setErrorPrompt(prompt $prompt)
	{
		$this->errorPrompt = $prompt;

		return $this;
	}

	public function getErrorPrompt()
	{
		return $this->errorPrompt;
	}

	public function setErrorColorizer(colorizer $colorizer)
	{
		$this->errorColorizer = $colorizer;

		return $this;
	}

	public function getErrorColorizer()
	{
		return $this->errorColorizer;
	}

	public function __toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$errors = $this->runner->getScore()->getErrors();

			$sizeOfErrors = sizeof($errors);

			if ($sizeOfErrors > 0)
			{
				$string .=
					$this->titlePrompt .
					sprintf(
						$this->locale->_('%s:'),
						$this->titleColorizer->colorize(sprintf($this->locale->__('There is %d error', 'There are %d errors', $sizeOfErrors), $sizeOfErrors))
					) .
					PHP_EOL
				;

				$class = null;
				$method = null;

				foreach ($errors as $error)
				{
					if ($error['class'] !== $class || $error['method'] !== $method)
					{
						$string .=
							$this->methodPrompt .
							sprintf(
								$this->locale->_('%s:'),
								$this->methodColorizer->colorize($error['class'] . '::' . $error['method'] . '()')
							) .
							PHP_EOL
						;

						$class = $error['class'];
						$method = $error['method'];
					}

					$string .= $this->errorPrompt;

					$type = self::getType($error['type']);

					if ($error['case'] === null)
					{
						switch (true)
						{
							case $error['file'] === null:
								switch (true)
								{
									case $error['errorFile'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in unknown file on unknown line, generated by unknown file'), $type);
										break;

									case $error['errorLine'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in unknown file on unknown line, generated by file %s'), $type, $error['errorFile']);
										break;

									case $error['errorLine'] !== null:
										$errorMessage = sprintf($this->locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d'), $type, $error['errorFile'], $error['errorLine']);
										break;
								}
								break;

							case $error['line'] === null:
								switch (true)
								{
									case $error['errorFile'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on unknown line, generated by unknown file'), $type, $error['file']);
										break;

									case $error['errorLine'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on unknown line, generated by file %s'), $type, $error['file'], $error['errorFile']);
										break;

									case $error['errorLine'] !== null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on unknown line, generated by file %s on line %d'), $type, $error['file'], $error['errorFile'], $error['errorLine']);
										break;
								}
								break;

							default:
								switch (true)
								{
									case $error['errorFile'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on line %d, generated by unknown file'), $type, $error['file'], $error['line']);
										break;

									case $error['errorLine'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on line %d, generated by file %s'), $type, $error['file'], $error['line'], $error['errorFile']);
										break;

									case $error['errorLine'] !== null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on line %d, generated by file %s on line %d'), $type, $error['file'], $error['line'], $error['errorFile'], $error['errorLine']);
										break;
								}
								break;
						}
					}
					else
					{
						switch (true)
						{
							case $error['file'] === null:
								switch (true)
								{
									case $error['errorFile'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in unknown file on unknown line in case \'%s\', generated by unknown file'), $type, $error['case']);
										break;

									case $error['errorLine'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in unknown file on unknown line, generated by file %s in case \'%s\''), $type, $error['errorFile'], $error['case']);
										break;

									case $error['errorLine'] !== null:
										$errorMessage = sprintf($this->locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\''), $type, $error['errorFile'], $error['errorLine'], $error['case']);
										break;
								}
								break;

							case $error['line'] === null:
								switch (true)
								{
									case $error['errorFile'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on unknown line, generated by unknown file in case \'%s\' in case \'%s\''), $type, $error['file'], $error['case']);
										break;

									case $error['errorLine'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on unknown line, generated by file %s in case \'%s\''), $type, $error['file'], $error['errorFile'], $error['case']);
										break;

									case $error['errorLine'] !== null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on unknown line, generated by file %s on line %d in case \'%s\''), $type, $error['file'], $error['errorFile'], $error['errorLine'], $error['case']);
										break;
								}
								break;

							default:
								switch (true)
								{
									case $error['errorFile'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on line %d, generated by unknown file in case \'%s\''), $type, $error['file'], $error['line'], $error['case']);
										break;

									case $error['errorLine'] === null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on line %d, generated by file %s in case \'%s\''), $type, $error['file'], $error['line'], $error['errorFile'], $error['case']);
										break;

									case $error['errorLine'] !== null:
										$errorMessage = sprintf($this->locale->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\''), $type, $error['file'], $error['line'], $error['errorFile'], $error['errorLine'], $error['case']);
										break;
								}
								break;
						}
					}

					$string .= sprintf(
							$this->locale->_('%s:'),
							$this->errorColorizer->colorize(($errorMessage))
						) .
						PHP_EOL
					;

					foreach (explode(PHP_EOL, $error['message']) as $line)
					{
						$string .= $line . PHP_EOL;
					}
				}
			}
		}

		return $string;
	}
}

?>
