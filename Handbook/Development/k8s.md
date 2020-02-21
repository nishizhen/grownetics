# Storage

We need a way to handle storage so that any node can go offline and things will keep working.

For this we are using Rook to deploy Ceph.

https://earlruby.org/2018/12/using-rook-ceph-for-persistent-storage-on-kubernetes/

https://akomljen.com/rook-cloud-native-on-premises-persistent-storage-for-kubernetes-on-kubernetes/

https://github.com/kubernetes/kubernetes/issues/64002#issuecomment-462288482

```
kubectl create -f ceph_common.yaml
kubectl create -f ceph_operator.yaml
kubectl create -f ceph_cluster.yaml
```

# Deploying

Deploy a fresh k8s cluster to physical hosts: `rke remove` to reset everything, then `rke up` to spin up fresh.

Trying to interact with helm you may hit `Error: no available release name found` or `Error: configmaps is forbidden: User "system:serviceaccount:kube-system:default" cannot list resource "configmaps" in API group "" in the namespace "kube-system"`

Run:
```
    kubectl --kubeconfig kube_config_cluster.yml create clusterrolebinding add-on-cluster-admin \
        --clusterrole=cluster-admin \
        --serviceaccount=kube-system:default
```

For easier kubectl/helm usage with the physical hosts run:
`export KUBECONFIG=~/Code/Grownetics/DevOps/kube_config_cluster.yml`

# k8s

To get a dashboard started:
```
kubectl apply -f https://raw.githubusercontent.com/kubernetes/dashboard/v1.10.1/src/deploy/recommended/kubernetes-dashboard.yaml
```

Then run `kubectl proxy`

Then load up http://localhost:8001/api/v1/namespaces/kube-system/services/https:kubernetes-dashboard:/proxy/

# To login (ref: https://github.com/kubernetes/dashboard/wiki/Creating-sample-user)
```
kubectl apply -f dashboard-user.yml,dashboard-user2.yml; kubectl -n kube-system describe secret $(kubectl -n kube-system get secret | grep admin-user | awk '{print $1}')
```
Copy the token, paste into token login field in dash.

# Kubectl

## Clear Out Existing Stack

kubectl delete -f common-config.yaml,appdb-claim0-persistentvolumeclaim.yaml,appdb-deployment.yaml,appdb-service.yaml,growdash-deployment.yaml,growdash-service.yaml,redis-deployment.yaml,redis-service.yaml,ip-space.yml

# Spin up MetalLB

kubectl apply -f https://raw.githubusercontent.com/google/metallb/v0.7.3/manifests/metallb.yaml

## Spin Up New Stack

kubectl apply -f common-config.yaml,appdb-claim0-persistentvolumeclaim.yaml,appdb-deployment.yaml,appdb-service.yaml,growdash-deployment.yaml,growdash-service.yaml,redis-deployment.yaml,redis-service.yaml,ip-space.yml

## Access Growdash

http://localhost:8001/api/v1/namespaces/default/services/growdash/proxy/

# Common Errors

Permission denied when pulling image

Deployment files need:
```
      imagePullSecrets:
      - name: regcred
```

Make sure to login with:
```
kubectl create secret docker-registry regcred --docker-server=code.cropcircle.io:4567 --docker-username=nick.b --docker-password=YOUR_DOCKER_TOKEN --docker-email=nick.b@grownetics.co
```

# Helm

Create a bookstack release: helm install stable/bookstack

Error: `helm tiller not found`
Solution: `helm init`

Created a bookstack helm release, to see status:
    helm status impressive-boxer
