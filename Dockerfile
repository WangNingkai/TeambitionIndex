# build frontend
FROM node:lts-buster AS fe-builder

COPY ./assets /assets

WORKDIR /assets

# yarn repo connection is unstable, adjust the network timeout to 10 min.
RUN set -ex \
    && yarn install --network-timeout 600000 \
    && yarn run build

# build backend
FROM golang:1.16-alpine3.12 AS be-builder

ENV GO111MODULE on
ENV GOPROXY=https://goproxy.io,direct

COPY . /go/src/teambition
COPY --from=fe-builder /assets/dist/ /go/src/teambition/assets/dist/

WORKDIR /go/src/teambition

RUN set -ex \
    && sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories \
    && apk upgrade \
    && apk add gcc libc-dev git \
    && export COMMIT_SHA=$(git rev-parse --short HEAD) \
    && export VERSION=$(git describe --tags) \
    && (cd && go get github.com/rakyll/statik) \
    && statik -f -src=assets/dist/ -include=*.html,*.js,*.json,*.css,*.png,*.svg,*.ico,*.woff2,*.woff \
    && go build -o /go/bin/teambition -ldflags "-w -s"

# build final image
FROM alpine:3.12 AS dist

LABEL maintainer="mritd <mritd@linux.com>"

# we use the Asia/Shanghai timezone by default, you can be modified
# by `docker build --build-arg=TZ=Other_Timezone ...`
ARG TZ="Asia/Shanghai"

ENV TZ ${TZ}

COPY --from=be-builder /go/bin/teambition /teambition/teambition

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories \
    && apk upgrade \
    && apk add bash tzdata \
    && ln -s /teambition/teambition /usr/bin/teambition \
    && ln -sf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone \
    && rm -rf /var/cache/apk/*

# teambition use tcp 5212 port by default
EXPOSE 3000/tcp

# teambition stores all files(including executable file) in the `/teambition`
# directory by default; users should mount the configfile to the `/etc/teambition`
# directory by themselves for persistence considerations, and the data storage
# directory recommends using `/data` directory.
VOLUME /etc/teambition

VOLUME /data

ENTRYPOINT ["teambition"]
