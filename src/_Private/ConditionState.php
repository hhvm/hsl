<?hh
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the hphp/hsl/ subdirectory of this source tree.
 *
 */

namespace HH\Lib\_Private;

use type HH\Lib\Async\Condition;

<<__Sealed(
  NotStarted::class,
  SyncResult::class,
  AsyncResult::class,
  Finished::class,
)>>
interface ConditionState<T> {
  public function waitForNotificationAsync(
    Condition<T> $condition,
    Awaitable<void> $notifiers,
  ): Awaitable<T>;
  public function trySucceed(Condition<T> $condition, T $result): bool;
  public function tryFail(Condition<T> $condition, \Exception $exception): bool;
}

final class NotStarted<T> implements ConditionState<T> {
  private function __construct() {}
  <<__Memoize>>
  public static function getInstance(): this {
    return new self();
  }

  public async function waitForNotificationAsync(
    Condition<T> $condition,
    Awaitable<void> $notifiers,
  ): Awaitable<T> {
    $handle = ConditionWaitHandle::create($notifiers);
    $condition->setState(new AsyncResult($handle));
    try {
      return await $handle;
    } finally {
      $condition->setState(Finished::getInstance());
    }
  }

  public function trySucceed(Condition<T> $condition, T $result): bool {
    $condition->setState(
      new SyncResult(
        async {
          return $result;
        },
      ),
    );
    return true;
  }
  public function tryFail(
    Condition<T> $condition,
    \Exception $exception,
  ): bool {
    $condition->setState(
      new SyncResult(
        async {
          throw $exception;
        },
      ),
    );
    return true;
  }
}

final class AsyncResult<T> implements ConditionState<T> {
  public function __construct(private ConditionWaitHandle<T> $resultHandle) {}
  public function waitForNotificationAsync(
    Condition<T> $_state_ref,
    Awaitable<void> $_notifiers,
  ): Awaitable<T> {
    invariant_violation('Unable to wait for notification twice');
  }
  public function trySucceed(Condition<T> $condition, T $result): bool {
    $this->resultHandle->succeed($result);
    return true;
  }
  public function tryFail(
    Condition<T> $condition,
    \Exception $exception,
  ): bool {
    $this->resultHandle->fail($exception);
    return true;
  }

}

final class SyncResult<T> implements ConditionState<T> {
  public function __construct(private Awaitable<T> $resultAwaitable) {}
  public function waitForNotificationAsync(
    Condition<T> $condition,
    Awaitable<void> $_notifiers,
  ): Awaitable<T> {
    $condition->setState(Finished::getInstance());
    return $this->resultAwaitable;
  }

  public function trySucceed(Condition<T> $condition, T $result): bool {
    return false;
  }
  public function tryFail(
    Condition<T> $condition,
    \Exception $exception,
  ): bool {
    return false;
  }
}

final class Finished<T> implements ConditionState<T> {
  private function __construct() {}
  <<__Memoize>>
  public static function getInstance(): this {
    return new self();
  }
  public function waitForNotificationAsync(
    Condition<T> $_state_ref,
    Awaitable<void> $_notifiers,
  ): Awaitable<T> {
    invariant_violation('Unable to wait for notification twice');
  }
  public function trySucceed(Condition<T> $condition, T $result): bool {
    return false;
  }
  public function tryFail(
    Condition<T> $condition,
    \Exception $exception,
  ): bool {
    return false;
  }
}
