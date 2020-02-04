FROM grownetics/php:1.0

RUN apt-get update && apt-get install -y \
         apt-transport-https \
         ca-certificates \
         curl \
         jq \
         gnupg2 \
         python-pip \
         wget \
         software-properties-common && \
         pip install docker-compose mkdocs mkdocs-material && \
    curl -fsSL https://download.docker.com/linux/debian/gpg | apt-key add - && \
    add-apt-repository \
       "deb [arch=amd64] https://download.docker.com/linux/debian \
       $(lsb_release -cs) \
       stable" && \
    apt-get update && apt-get install -y docker-ce && \
    wget https://releases.rancher.com/cli/v0.6.1/rancher-linux-amd64-v0.6.1.tar.gz && \
    tar -xvzf rancher-linux-amd64-v0.6.1.tar.gz && \
    mv rancher-v0.6.1/rancher /usr/local/bin/

ENV DOCKER_TLS_CERTDIR=/certs
# also, ensure the directory pre-exists and has wide enough permissions for "dockerd-entrypoint.sh" to create subdirectories, even when run in "rootless" mode
RUN mkdir /certs /certs/client && chmod 1777 /certs /certs/client
