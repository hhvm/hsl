<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{C, Vec};

/**
 * Iterator that implements the same behavior as generators when
 * Hack.Lang.AutoprimeGenerators is false
 */
final class HackLibTestForwardOnlyIterator<Tk as arraykey, Tv>
implements \HH\Rx\Iterator<Tv>, \HH\Rx\KeyedIterator<Tk, Tv> {
  private bool $used = false;
  private int $keyIdx = 0;
  private vec<Tk> $keys;

  <<__Pure>>
  public function __construct(private dict<Tk, Tv> $data) {
    /* HH_FIXME[4200] Mark things as pure to unblock releasing hack */
    $this->keys = Vec\keys($data);
  }

  <<__Pure, __MaybeMutable>>
  public function current(): Tv  {
    return $this->data[$this->keys[$this->keyIdx]];
  }

  <<__Pure, __MaybeMutable>>
  public function key(): Tk {
    return $this->keys[$this->keyIdx];
  }

  <<__Pure, __Mutable>>
  public function rewind(): void {
    if ($this->used) {
      $this->next();
      $this->used = false;
    }
  }

  <<__Pure, __MaybeMutable>>
  public function valid(): bool {
    /* HH_FIXME[4200] Mark things as pure to unblock releasing hack */
    return C\contains_key($this->keys, $this->keyIdx);
  }

  <<__Pure, __Mutable>>
  public function next(): void {
    $this->used = true;
    $this->keyIdx++;
  }
}
