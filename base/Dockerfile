# ----- Stage 1: Build BusyBox with glibc -----
FROM debian:bookworm-slim AS build

ENV DEBIAN_FRONTEND=noninteractive \
    BUSYBOX_VERSION=1.37.0 \
    BUSYBOX_URL=https://busybox.net/downloads \
    BUSYBOX_SHA256=3311dff32e746499f4df0d5df04d7eb396382d7e108bb9250e7b519b837043a4

WORKDIR /root

# Use BuildKit RUN mounts to cache apt directories.
# https://docs.docker.com/build/cache/optimize/#use-cache-mounts
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    sed -i 's/\(Types: deb\)/\1 deb-src/g' /etc/apt/sources.list.d/debian.sources && \
    apt update && \
    apt install -y --no-install-recommends \
        dpkg-dev \
        build-essential \
        libpam0g-dev && \
    apt build-dep -y busybox-static

ADD --checksum=sha256:${BUSYBOX_SHA256} ${BUSYBOX_URL}/busybox-${BUSYBOX_VERSION}.tar.bz2 .

WORKDIR /root/busybox

COPY init.c.patch .
COPY make.config .config

# Build a static BusyBox suitable for an OCI environment.
RUN set -eux; \
    tar -xjf ../busybox-${BUSYBOX_VERSION}.tar.bz2 --strip=1 -C . && \
    # Patch the included init to make it more container friendly
    patch -p0 < init.c.patch && \
    # Patch all legacy paths to the unified /usr dir e.g. /bin -> /usr/bin
    find . \( -name "*.c" -o -name "*.h" \) -print0 | xargs -0 sed -i \
        -e 's/\/bin/\/usr\/bin/g' \
        -e 's/\/sbin/\/usr\/bin/g' \
        -e 's/\/lib/\/usr\/lib/g' \
        -e 's/\/lib64/\/usr\/lib/g' && \
    make -j $(nproc || echo 4) && \
    strip busybox

# ----- Stage 2: Build a minimalistic base with BusyBox (glibc) -----
FROM scratch

COPY --from=build --chmod=755 --chown=0:0 /root/busybox/busybox /usr/bin/busybox
COPY --chmod=755 etc /etc
COPY --chmod=755 usr /usr

SHELL ["/usr/bin/busybox", "sh", "-c"]

RUN busybox --install -s /usr/bin && \
    mkdir -p /etc/healthcheck.d \
        /etc/init.d/start \
        /etc/init.d/stop

ENV SHELL=/usr/bin/sh \
    HEALTHCHECK_PIPE=/dev/healthcheck

CMD ["/usr/bin/init"]

# Upon a healthcheck failure signal the container to shutdown,
# the rest is up to the restart policy.
HEALTHCHECK --interval=30s --timeout=10s --start-period=0s --retries=0 \
    CMD [ "/usr/bin/sh", "-c", "test $(cat $HEALTHCHECK_PIPE) -eq 0 || { kill -USR2 1 && exit 1; }" ]
