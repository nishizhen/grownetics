FROM golang

#RUN curl -s https://glide.sh/get | sh
#RUN apt-get update && apt-get install netcat -y

ADD . /go/src/
WORKDIR /go/src
#RUN cd /go/src/code.cropcircle.io/grownetics/growctl && glide install
RUN go get gitlab.com/grownetics/grownetics/growctl && go build -o growctl gitlab.com/grownetics/grownetics/growctl
CMD ["./growctl","faker","--url","http://nginx:81/","--json"]

# This shit is too future for us right now.
# Multi-stage build that produces a much smaller image

#FROM golang:1.8.3 as builder
#
#ADD . /go/src/
#WORKDIR /go/src
#RUN go get code.cropcircle.io/grownetics/growctl && CGO_ENABLED=0 GOOS=linux go build -a -o growctl code.cropcircle.io/grownetics/growctl
#CMD ["./growctl","faker","--url","http://nginx:81/","--json"]
#
#
#
#FROM scratch
#COPY --from=builder /go/src/growctl .
#CMD ["./growctl"]