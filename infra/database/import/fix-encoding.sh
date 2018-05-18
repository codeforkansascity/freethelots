#!/usr/bin/env bash

set -o errexit
set -o nounset
set -o pipefail

src_gz="${1}"
dest="${2}"

# Convert to UTF8
gzcat "${src_gz}" | recode windows-1252..utf8 > "${dest}"
# remove delinquent CRCRLF (found with in a field) and delete all other CR.
perl -0777pi -e 's/\r\r\n//g;' -e 's/\r//g;' "${dest}"
