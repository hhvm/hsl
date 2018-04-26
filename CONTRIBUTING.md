# Contributing to the Hack Standard Library
We want to make contributing to this project as easy and transparent as
possible.

## Feature Requests

New features should be contributed to
[the experimental repository](https://github.com/hhvm/hsl-experimental/)
instead; if you believe an experimental feature is mature, please file an
issue to request the relevant files be moved to this repository.

## Our Development Process

The source of truth for this library is an internal repository; we continuously
sync changes out to GitHub using
[FBShipIt](https://github.com/facebook/fbshipit), and test/apply pull requests
against the repository after review.

If a pull request passes the public tests but has internal issues, we will not
be able to merge the pull request until the internal issues are fixed; depending
on the scale of the problem, this can take a few weeks.

## Pull Requests
We actively welcome your pull requests.

1. Fork the repo and create your branch from `master`.
2. If you've added code that should be tested, add tests.
3. If you've changed APIs, update the documentation.
4. Ensure the test suite passes.
5. Make sure your code lints.
6. If you haven't already, complete the Contributor License Agreement ("CLA").

## Contributor License Agreement ("CLA")
In order to accept your pull request, we need you to submit a CLA. You only need
to do this once to work on any of Facebook's open source projects.

Complete your CLA here: <https://code.facebook.com/cla>

## Issues
We use GitHub issues to track public bugs. Please ensure your description is
clear and has sufficient instructions to be able to reproduce the issue.

Facebook has a [bounty program](https://www.facebook.com/whitehat/) for the safe
disclosure of security bugs. In those cases, please go through the process
outlined on that page and do not file a public issue.

## Coding Style

* 2 spaces for indentation rather than tabs
* 80 character line length
* Be consistent with existing code
* Be consistent with hackfmt

We do not follow the PSR guidelines.

## License
By contributing to the Hack Standard Library, you agree that your contributions
will be licensed under the LICENSE file in the root directory of this source
tree.
