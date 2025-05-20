# laravel-sli


## このリポジトリの目的
SLIやSLOといった観点で監視を始めるに当たって
好きに触って確認できる環境を用意したい。

インスタンスやALBなどの料金が加算されてしまうので、いくつかコマンドを実行するだけで
Laravel + grafanaの環境が起動させることができ
すぐに閉じれるようにする

## terraformでインフラ部分の構築

version.tfの下記を自分の利用するバケットに書き換える
```tf
  backend "s3" {
    bucket  = "tskt-terraform" <-ここ
```

applyするとECSクラスタまで作成される
```sh
cd terraform
terraform init
terraform plan
terraform apply
```

## docker build & push
AWSアカウントのIDなどは利用するアカウントのものに書き換えて実行すること

```sh
#ecr login
aws ecr get-login-password --region ap-northeast-1 | docker login --username AWS --password-stdin 031215319865.dkr.ecr.ap-northeast-1.amazonaws.com


# マルチプラットフォームビルド
export DOCKER_BUILDKIT=1 
docker buildx create --name mybuilder --use;
docker buildx inspect --bootstrap 

docker buildx build \
  --platform linux/amd64,linux/arm64 \
  -f ./docker/php/ecs/dockerfile \
  -t public.ecr.aws/v9g2r9e2/wwwtskt/laravel:latest \
  --push \
  .

docker buildx build \
  --platform linux/amd64,linux/arm64 \
  -f ./docker/nginx/ecs/dockerfile \
  -t public.ecr.aws/v9g2r9e2/wwwtskt/nginx:latest \
  --push \
  .

docker buildx build \
  --platform linux/amd64,linux/arm64 \
  -f ./docker/fluent-bit/ecs/dockerfile \
  -t public.ecr.aws/v9g2r9e2/wwwtskt/fluent-bit:latest \
  --push \
  .

```

## ECSのサービス以下を管理するecspresso

参考：https://github.com/kayac/ecspresso

```sh

# ecs presspで既存のimport
ecspresso init \
  --region ap-northeast-1 \
  --cluster laravel-sli-cluster \
  --service laravel-sli-service \
  --config ecspresso.yml

# deploy
ecspresso deploy --config ecspresso.yml --envfile .env

# 変更差分
ecspresso diff --config ecspresso.yml --envfile .env

```

test
