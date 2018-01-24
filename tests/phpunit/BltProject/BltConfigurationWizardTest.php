<?php

namespace Acquia\Blt\Tests\Blt;

use Acquia\Blt\Tests\BltProjectTestBase;
use Symfony\Component\Yaml\Yaml;
use Acquia\Blt\Robo\Config\ConfigAwareTrait;
use Acquia\Blt\Tests\BltCommandTester;
use Acquia\Blt\Robo\Config\ConfigInitializer;

/**
 * Class BltConfigurationWizard.
 */
class BltConfigurationWizardTest extends BltProjectTestBase {

  use ConfigAwareTrait;

  /**
   * Tests setup:wizard command.
   */
  public function testWizard() {
    $command = 'setup:wizard';
    $args = [];

    $input = $this->createBltInput($command, $args);

    $config_initializer = new ConfigInitializer($this->sandboxInstance, $input);
    $config = $config_initializer->initialize();

    $commandTester = new BltCommandTester($config);

    $commandTester->setInputs([
      // Project title (human readable):
      'Test Project',
      // Project machine name:
      'TEST',
      // Project prefix:
      'TST',
      // Do you want to create a VM?
      'n',
      // Do you want to use Continuous Integration?
      'y',
      // Choose a Continuous Integration provider:
      'pipelines',
      // Continue?
      'y',
    ]);

    $commandTester->execute(['command'  => $command]);

    $recipe = Yaml::parse(
      file_get_contents('recipe.yml')
    );

    /*
     * TODO: Determine if there is a better way to test this.
     *
     * The project.yml file seems to be missing most of the values you'd expect.
     * Is the setup for the PHPUnit tests not copying over the default file as
     * you would expect?
     */
    $project_configuration = Yaml::parse(
      file_get_contents('blt/project.yml')
    );

    $config_keys = [
      'human_name',
      'machine_name',
      'prefix',
      'vm',
    ];

    foreach($config_keys as $key) {
      $this->assertEquals($recipe[$key], $project_configuration['project'][$key]);
    }

    return;

    $status_code = $commandTester->getStatusCode();

    $this->assertEquals($status_code, 0, "The command create-project exited with a non-zero code: " . $commandTester->getDisplay());

    $output = $commandTester->getDisplay();

//    $this->markTestIncomplete(
//      'This test has not been implemented yet.'
//    );
  }

  /**
   * Tests setup:wizard with recipe file option
   */
  public function testWizardUsingRecipe() {
    $this->blt("setup:wizard", ['--recipe' => 'recipe.yml']);

    $recipe = Yaml::parse(
      file_get_contents('recipe.yml')
    );

    /*
     * TODO: Determine if there is a better way to test this.
     *
     * The project.yml file seems to be missing most of the values you'd expect.
     * Is the setup for the PHPUnit tests not copying over the default file as
     * you would expect?
     */
    $project_configuration = Yaml::parse(
      file_get_contents('blt/project.yml')
    );

    $config_keys = [
      'human_name',
      'machine_name',
      'prefix',
      'vm',
    ];

    foreach($config_keys as $key) {
      $this->assertEquals($recipe[$key], $project_configuration['project'][$key]);
    }

  }

}
