<?php

namespace Acquia\Blt\Tests;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Acquia\Blt\Robo\Blt;

class BltCommandTester extends CommandTester {

  public $bltConfig;

  public $input;

  public $output;

  public $inputs = [];

  public $statusCode;

  /**
   * We do not want to pass in a command.
   */
  public function __construct($blt_config) {
    $this->bltConfig = $blt_config;
  }

  /**
   * Mostly duplicated from CommandTester, but we call the BLT command and not
   * the Symfony command.
   */
  public function execute(array $input, array $options = []) {

    // A check for the command name was removed here. The command must be set in
    // the input options.

    $this->input = new ArrayInput($input);
    if ($this->inputs) {
      $this->input->setStream(self::createStream($this->inputs));
    }

    if (isset($options['interactive'])) {
      $this->input->setInteractive($options['interactive']);
    }

    $this->output = new StreamOutput(fopen('php://memory', 'w', false));
    $this->output->setDecorated(isset($options['decorated']) ? $options['decorated'] : false);
    if (isset($options['verbosity'])) {
      $this->output->setVerbosity($options['verbosity']);
    }

    // These lines are modified to run the blt command.
    $blt = new Blt($this->bltConfig, $this->input, $this->output);
    return $this->statusCode = (int) $blt->run($this->input, $this->output);
  }

  /**
   * Duplicated from CommandTester.
   */
  public function getDisplay($normalize = false)
  {
    rewind($this->output->getStream());

    $display = stream_get_contents($this->output->getStream());

    if ($normalize) {
      $display = str_replace(PHP_EOL, "\n", $display);
    }

    return $display;
  }

  /**
   * Duplicated from CommandTester.
   */
  public function getInput()
  {
    return $this->input;
  }

  /**
   * Duplicated from CommandTester.
   */
  public function getOutput()
  {
    return $this->output;
  }

  /**
   * Duplicated from CommandTester.
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }

  /**
   * Duplicated from CommandTester.
   */
  public function setInputs(array $inputs)
  {
    $this->inputs = $inputs;

    return $this;
  }

  /**
   * Duplicated from CommandTester.
   */
  private static function createStream(array $inputs)
  {
    $stream = fopen('php://memory', 'r+', false);

    fwrite($stream, implode(PHP_EOL, $inputs));
    rewind($stream);

    return $stream;
  }
}
