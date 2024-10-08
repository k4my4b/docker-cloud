#!/usr/bin/env sh

# Default starting directory is /init.d/start if not passed as argument
INDIR="${1:-/init.d/start}"

# Default stoppping directory is /init.d/stop if not passed as argument
EDIR="${2:-/init.d/stop}"

batch_and_run_in_parallel() {
    # Find and sort scripts
    find "$1" -type f -executable -name '[0-9]?-*.sh' | sort -V | \
    # Group the scripts of the same priority together
    awk '
    {
        split($0, arr, "/")
        file = arr[length(arr)]
        num = substr(file, 1, index(file, "-") - 1)
        if (num != prev) {
            if (NR > 1) {
                print ""
            }
            prev = num
        }
        printf "%s ", $0
    }
    END { print "" }
    ' | \
    # Execute scripts of the same priority (same group) in parallel
    # Exit as soon as a script fails to execute properly
    while IFS= read -r scripts; do
        parallel --will-cite --halt 2 -k -u -d ' ' {} ::: $scripts || {
		kill -TERM 1
		exit 1
	}
    done
}

trap 'batch_and_run_in_parallel "$EDIR"' TERM EXIT

batch_and_run_in_parallel "$INDIR"
