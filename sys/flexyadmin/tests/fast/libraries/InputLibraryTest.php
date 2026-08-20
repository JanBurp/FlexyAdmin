<?php

require_once(APPPATH.'/tests/CITestCase.php');

/**
 * Regression test for MY_Input's restored global input sanitizing (see MY_Input.php
 * docblock): pocketarc/codeigniter dropped CI3's eager sanitize-globals pipeline and
 * no longer honours $config['global_xss_filtering'] at all, so this was rebuilt in
 * MY_Input to keep working like stock CI3 3.1.13 for FlexyAdmin sites that rely on it.
 *
 * @author Jan den Besten
 */
class InputLibraryTest extends CITestCase {

  private $enable_xss_prop;

  protected function setUp() :void {
    // Reflection handle on the protected $_enable_xss property, so we can flip
    // $config['global_xss_filtering'] on/off for a single call without rebooting
    // the whole app (it's normally only read once, in MY_Input::__construct()).
    $this->enable_xss_prop = new ReflectionProperty('MY_Input', '_enable_xss');
    $this->enable_xss_prop->setAccessible(true);
  }

  protected function tearDown() :void {
    // Always leave global_xss_filtering as FlexyAdmin's config has it
    $this->enable_xss_prop->setValue($this->CI->input, (config_item('global_xss_filtering') === TRUE));
  }

  public function testCleanInputKeysRejectsDisallowedCharacters() {
    $clean = new ReflectionMethod('MY_Input', '_clean_input_keys');
    $clean->setAccessible(true);

    // Allowed: alpha-numeric, colon, underscore, slash, pipe, dash
    $this->assertSame('a-b_c:d/e|f', $clean->invoke($this->CI->input, 'a-b_c:d/e|f'));

    // Disallowed characters, $fatal=TRUE (as used by _sanitize_globals()) -> FALSE, not fatal
    $this->assertFalse($clean->invoke($this->CI->input, '<script>', TRUE));
  }

  public function testGlobalXssFilteringOffLeavesDataUntouched() {
    $this->enable_xss_prop->setValue($this->CI->input, FALSE);

    $_POST['xss_test_field'] = '<script>alert(1)</script>';
    $this->assertSame('<script>alert(1)</script>', $this->CI->input->post('xss_test_field'));
    unset($_POST['xss_test_field']);
  }

  public function testGlobalXssFilteringOnCleansDataWithoutExplicitFlag() {
    $this->enable_xss_prop->setValue($this->CI->input, TRUE);

    $_POST['xss_test_field'] = '<script>alert(1)</script>';
    $cleaned = $this->CI->input->post('xss_test_field');
    $this->assertStringNotContainsString('<script>', $cleaned);
    unset($_POST['xss_test_field']);
  }

  public function testExplicitFlagAlwaysOverridesGlobalSetting() {
    // Global OFF, but caller explicitly asks for cleaning -> still cleaned
    $this->enable_xss_prop->setValue($this->CI->input, FALSE);
    $_POST['xss_test_field'] = '<script>alert(1)</script>';
    $this->assertStringNotContainsString('<script>', $this->CI->input->post('xss_test_field', TRUE));

    // Global ON, but caller explicitly opts out -> left untouched
    $this->enable_xss_prop->setValue($this->CI->input, TRUE);
    $this->assertSame('<script>alert(1)</script>', $this->CI->input->post('xss_test_field', FALSE));
    unset($_POST['xss_test_field']);
  }
}
