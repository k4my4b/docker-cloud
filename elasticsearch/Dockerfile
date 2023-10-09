FROM elasticsearch:7.17.13

ENV discovery.type=single-node \
    bootstrap.memory_lock=true \
    ES_JAVA_OPTS="-Xms1024M -Xmx1024M"

RUN elasticsearch-plugin install --batch ingest-attachment

HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 \
    CMD curl -sfL "http://localhost:9200/_cat/health" || exit 1 