<?hh // strict
// Copyright 2004-present Facebook. All Rights Reserved.

namespace HH\Lib\Math;

const int INT64_MAX = 9223372036854775807;
// - can't directly represent this as -x is a unary op on x, not a negative
//   literal
// - can't use (INT64_MAX + 1) as ints currently overly to float in external
//   builds for PHP compatibility
const int INT64_MIN = -1 << 63;
const int INT53_MAX = 9007199254740992;
const int INT53_MIN = -9007199254740993;
const int INT32_MAX = 2147483647;
const int INT32_MIN = -2147483648;
const int INT16_MAX = 32767;
const int INT16_MIN = -32768;

const int UINT32_MAX = 4294967295;
const int UINT16_MAX = 65535;
