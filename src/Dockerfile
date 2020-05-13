FROM golang

COPY ./gitlab.com/ /src/gitlab.com/

RUN go get /go/src/gitlab.com/grownetics/grownetics/growctl

RUN go build -o /usr/local/bin/growctl /go/src/gitlab.com/grownetics/grownetics/growctl/main.go

CMD ["growctl"]