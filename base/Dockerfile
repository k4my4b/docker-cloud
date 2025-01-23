FROM alpine:3.19

RUN apk add --no-cache parallel && rm -fr /etc/periodic /etc/crontabs/root

COPY --chmod=775 init.d /init.d
COPY --chmod=755 init.sh /init.sh
COPY --chmod=775 healthcheck.d /healthcheck.d
COPY --chmod=755 healthcheck.sh /healthcheck.sh

ENTRYPOINT ["/init.sh"]
CMD ["/init.d/start", "/init.d/stop"]

HEALTHCHECK CMD ["/healthcheck.sh", "/healthcheck.d"]
