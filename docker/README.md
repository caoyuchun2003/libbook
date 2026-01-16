# Docker 配置文件说明

## 文件结构

```
docker/
├── nginx/
│   └── backend.conf          # 后端 Nginx 配置
└── init-backend.sh           # 后端初始化脚本
```

## 配置文件说明

### backend.conf

Nginx 反向代理配置，用于：
- 将请求转发到 PHP-FPM (backend 容器)
- 处理静态文件
- 配置 Gzip 压缩
- 设置安全头

### init-backend.sh

后端容器启动时的初始化脚本，执行：
- 等待数据库就绪
- 清除缓存
- 运行数据库迁移

## 使用方式

这些配置文件会被 `docker-compose.yml` 自动使用，无需手动配置。
