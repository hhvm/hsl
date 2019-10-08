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
 * See detailed warning at top of `BasePoll`
 *
 * ===== WARNING ===== WARNING ===== WARNING ===== WARNING ===== WARNING =====
 */

final class Poll<Tv> extends BasePoll<mixed, Tv> implements AsyncIterator<Tv> {

  public static function from(Traversable<Awaitable<Tv>> $awaitables): this {
    return self::fromImpl(new Vector($awaitables));
  }

  public function add(Awaitable<Tv> $awaitable): void {
    $this->addImpl(null, $awaitable);
  }

  public function addMulti(Traversable<Awaitable<Tv>> $awaitables): void {
    $this->addMultiImpl(new Vector($awaitables));
  }
}
