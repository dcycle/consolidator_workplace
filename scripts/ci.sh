#!/bin/bash
#
# Run all tests and checks on a continuous integration environment.
#

set -e

./scripts/test.sh
./scripts/deploy.sh
