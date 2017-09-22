<?hh //strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

namespace HH\Lib\__Private;

use namespace HH\Lib\Str;

abstract class TestCase extends \PHPUnit_Framework_TestCase {
  public function __construct(mixed ...$args) {
    parent::__construct(...$args);

    // @oss-disable: return;

    $rc = new \ReflectionClass(static::class);
    foreach ($rc->getMethods() as $method) {
      if (!$method->isPublic()) {
        continue;
      }
      if (!Str\starts_with($method->getName(), 'test')) {
        continue;
      }
      $type = (string) $method->getReturnType();
      if (!Str\starts_with($type, 'HH\\Awaitable')) {
        continue;
      }
      self::fbIntercept(
        static::class.'::'.$method->getName(),
        self::class.'::deAsyncify',
      );
    }
  }

  public static function deAsyncify(
    string $name,
    mixed $obj,
    array<mixed> $params,
    mixed $_data,
    bool $done,
  ): mixed {
    invariant(
      $done === true,
      "Can't set \$done without references, need it to be true",
    );
    try {
      self::fbIntercept($name, null);
      $value = \HH\Asio\join(
        (new \ReflectionMethod($name))->invokeArgs($obj, $params),
      );
      invariant(
        !$value instanceof Awaitable,
        'got an Awaitable<Awaitable>>',
      );
    } finally {
      self::fbIntercept($name, static::class.'::deAsyncify');
    }
    return $value;
  }

  private static function fbIntercept(string $name, ?string $handler): void {
    // UNSAFE_EXPR
    \fb_intercept($name, $handler);
  }
}
