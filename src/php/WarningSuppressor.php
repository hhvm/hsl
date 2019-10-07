<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\PHP;

/**
 * Many PHP builtins emit warnings to stderr when they fail. This
 * class allows us to squash warnings for a time without using PHP's
 * `@` annotation.
 */
final class WarningSuppressor implements \IDisposable {

  private int $warningLevel;

  public function __construct() {
    /* HH_IGNORE_ERROR[2049] __PHPStdLib */
    /* HH_IGNORE_ERROR[4107] __PHPStdLib */
    $this->warningLevel = \error_reporting(0);
  }

  public function __dispose(): void {
    /* HH_IGNORE_ERROR[2049] __PHPStdLib */
    /* HH_IGNORE_ERROR[4107] __PHPStdLib */
    \error_reporting($this->warningLevel);
  }
}
