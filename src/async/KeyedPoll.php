<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Async;

/**
 * ===== WARNING ===== WARNING ===== WARNING ===== WARNING ===== WARNING =====
 *
 * See detailed warning for `BasePoll`
 *
 * ===== WARNING ===== WARNING ===== WARNING ===== WARNING ===== WARNING =====
 */
final class KeyedPoll<Tk, Tv>
  extends BasePoll<Tk, Tv>
  implements AsyncKeyedIterator<Tk, Tv> {

  public static function from(
    KeyedTraversable<Tk, Awaitable<Tv>> $awaitables,
  ): this {
    return self::fromImpl($awaitables);
  }

  public function add(Tk $key, Awaitable<Tv> $awaitable): void {
    $this->addImpl($key, $awaitable);
  }

  public function addMulti(
    KeyedTraversable<Tk, Awaitable<Tv>> $awaitables,
  ): void {
    $this->addMultiImpl($awaitables);
  }
}
