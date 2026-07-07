pipeline {
    agent any

    environment {
        DEPLOY_HOST = '180.76.180.105'
        DEPLOY_USER = 'root'
        APP_DIR = '/opt/apps/libbook'
        STATIC_DIR = '/opt/nginx/html/libbook'
        NODE_IMAGE = 'docker.io/library/node:20-alpine'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Frontend') {
            steps {
                sh '''
                    docker run --rm \
                        -v "$PWD/frontend:/app" -w /app \
                        -e VITE_BASE=/libbook/ \
                        ${NODE_IMAGE} sh -c "
                            set -e
                            npm ci || npm install
                            npm run build
                        "
                '''
            }
        }

        stage('Deploy') {
            steps {
                sh '''
                    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} \
                        "mkdir -p ${STATIC_DIR} ${APP_DIR}/backend ${APP_DIR}/docker"

                    rsync -avz --delete \
                        -e "ssh -o StrictHostKeyChecking=no" \
                        frontend/dist/ ${DEPLOY_USER}@${DEPLOY_HOST}:${STATIC_DIR}/

                    rsync -avz \
                        -e "ssh -o StrictHostKeyChecking=no" \
                        --exclude '.git' \
                        --exclude 'node_modules' \
                        --exclude 'frontend/node_modules' \
                        --exclude 'frontend/dist' \
                        --exclude '.DS_Store' \
                        backend/ ${DEPLOY_USER}@${DEPLOY_HOST}:${APP_DIR}/backend/

                    scp -o StrictHostKeyChecking=no docker-compose.gateway.yml \
                        ${DEPLOY_USER}@${DEPLOY_HOST}:${APP_DIR}/docker-compose.yml

                    rsync -avz \
                        -e "ssh -o StrictHostKeyChecking=no" \
                        docker/ ${DEPLOY_USER}@${DEPLOY_HOST}:${APP_DIR}/docker/

                    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} bash << 'ENDSSH'
                        set -e
                        cd /opt/apps/libbook
                        if command -v podman-compose >/dev/null 2>&1; then
                            podman-compose down || true
                            podman-compose up -d --build
                        elif docker compose version >/dev/null 2>&1; then
                            docker compose down || true
                            docker compose up -d --build
                        else
                            docker-compose down || true
                            docker-compose up -d --build
                        fi
                        docker exec nginx nginx -s reload || true
ENDSSH
                '''
            }
        }
    }

    post {
        success {
            echo '✅ 书城部署成功: http://180.76.180.105/libbook/'
        }
        failure {
            echo '❌ 书城部署失败'
        }
    }
}
