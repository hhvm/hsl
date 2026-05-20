<?hh
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the hphp/hsl/ subdirectory of this source tree.
 *
 */

namespace HH\Lib\Async;

use namespace HH\Lib\_Private;

/**
 * A wrapper around ConditionWaitHandle that allows notification events
 * to occur before the condition is awaited.
 */
class Condition<T> implements ConditionNotifyee<T> {
  private _Private\ConditionState<T> $state;
  public function __construct() {
    $this->state = _Private\NotStarted::getInstance();
  }

  public function setState(_Private\ConditionState<T> $state): void {
    $this->state = $state;
  }

  final public function succeed(T $result): void {
    invariant(
      $this->state->trySucceed($this, $result),
      'Unable to notify Condition twice',
    );
  }

  final public function fail(\Exception $exception): void {
    invariant(
      $this->state->tryFail($this, $exception),
      'Unable to notify Condition twice',
    );
  }

  final public function trySucceed(T $result): bool {
    return $this->state->trySucceed($this, $result);
  }

  final public function tryFail(\Exception $exception): bool {
    return $this->state->tryFail($this, $exception);
  }

  /**
   * Asynchronously wait for the condition variable to be notified and
   * return the result or throw the exception received via notification.
   *
   * The caller must provide an Awaitable $notifiers (which must be a
   * WaitHandle) that must not finish before the notification is received.
   * This means $notifiers must represent work that is guaranteed to
   * eventually trigger the notification. As long as the notification is
   * issued only once, asynchronous execution unrelated to $notifiers is
   * allowed to trigger the notification.
   */
  final public async function waitForNotificationAsync(
    Awaitable<void> $notifiers,
  ): Awaitable<T> {
    return await $this->state->waitForNotificationAsync($this, $notifiers);
  }
}


/**
 * Asynchronously wait for the condition variable to be notified and
 * return the result or throw the exception received via notification.
 *
 * The caller must provide an Awaitable $notifiers (which must be a
 * WaitHandle) that must not finish before the notification is received.
 * This means $notifiers must represent work that is guaranteed to
 * eventually trigger the notification. As long as the notification is
 * issued only once, asynchronous execution unrelated to $notifiers is
 * allowed to trigger the notification.
 */
function wait_for_notification_async<T>(
  (function(ConditionNotifyee<T>): Awaitable<void>) $notifiers,
): Awaitable<T> {
  $condition = new Condition();
  return $condition->waitForNotificationAsync($notifiers($condition));
}

interface ConditionNotifyee<-T> {

  /**
   * Notify the condition variable of success and set the $result.
   */
  public function succeed(T $result): void;
  /**
   * Notify the condition variable of success and set the $result.
   *
   * @return
   *   true if the condition is set to $result successfully, false if the
   *   condition was previously set to another result or exception.
   */
  public function trySucceed(T $result): bool;

  /**
   * Notify the condition variable of failure and set the exception.
   */
  public function fail(\Exception $exception): void;
  /**
   * Notify the condition variable of failure and set the $exception.
   *
   * @return
   *   true if the condition is set to $exception successfully, false if the
   *   condition was previously set to another result or exception.
   */
  public function tryFail(\Exception $exception): bool;
}
