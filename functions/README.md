# Libbook · 百度 CFC HTTPS 代理

```text
浏览器 (https://libbook.yuchuntest.com)
    │  HTTPS
    ▼
CFC  https://xxxx.cfc-execute.bj.baidubce.com/libbook/api/...
    │  HTTP
    ▼
BCC  http://180.76.180.105/libbook/api/...
```

## 部署

```bash
pip3 install --user bce-sam-cli 'werkzeug<3'
export PATH="$HOME/Library/Python/3.9/bin:$PATH"
bsam config --ak '百度云AK' --sk '百度云SK' --region bj

cd functions
chmod +x scripts/*.sh
./scripts/deploy.sh
./scripts/set-github-secret.sh 'https://xxxx.cfc-execute.bj.baidubce.com'
```

## 探活

```bash
curl -s 'https://xxxx.cfc-execute.bj.baidubce.com/'
curl -s 'https://xxxx.cfc-execute.bj.baidubce.com/libbook/api/books'
# 未登录一般为 401 {"message":"未认证"}
```
