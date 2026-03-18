# Simple PHP API Project with Kubernetes Deployment

## Overview

This project creates a simple PHP REST API and automates its deployment to Kubernetes. The project includes all necessary configurations for containerization and orchestration.

## Project Structure

```
simple_k8k_project/
├── app/
│   └── index.php          # PHP application
├── docker/
│   ├── nginx/
│   │   └── default.conf   # Nginx configuration
│   └── start.sh           # Container startup script
├── k8s/
│   ├── deployment.yaml    # Kubernetes Deployment
│   ├── service.yaml       # Kubernetes Service
│   └── ingress.yaml       # Kubernetes Ingress
├── Dockerfile             # Docker image definition
└── README.md              # This file
```

## Application Details

### PHP API (app/index.php)

A simple REST API without frameworks providing:
- `GET /` - API info and available endpoints
- `GET /health` - Health check endpoint (used by Kubernetes probes)
- `GET /hello` - Returns "Hello, World!"
- `GET /hello/{name}` - Returns personalized greeting

### Docker Configuration

**Dockerfile:**
- Base image: `php:8.2-fpm-alpine`
- Includes nginx for serving requests
- Uses a startup script to run both nginx and php-fpm

**Nginx Configuration:**
- Listens on port 80
- Routes all requests to index.php
- Proxies PHP requests to php-fpm on port 9000

### Kubernetes Manifests

**Deployment (k8s/deployment.yaml):**
- 2 replicas for high availability
- Resource limits: 128Mi memory, 100m CPU
- Liveness and readiness probes on `/health` endpoint

**Service (k8s/service.yaml):**
- ClusterIP type for internal access
- Exposes port 80

**Ingress (k8s/ingress.yaml):**
- Uses nginx ingress controller
- Host: `php-api.local`
- Routes all traffic to the php-api service

## Deployment Instructions

### 1. Build the Docker Image

```bash
docker build -t php-api:latest .
```

### 2. Load Image to Kubernetes (for local clusters)

For Minikube:
```bash
minikube image load php-api:latest
```

For Kind:
```bash
kind load docker-image php-api:latest
```

### 3. Apply Kubernetes Manifests

```bash
kubectl apply -f k8s/
```

### 4. Verify Deployment

```bash
# Check pods
kubectl get pods

# Check services
kubectl get svc

# Check ingress
kubectl get ingress
```

### 5. Access the Application

Add to `/etc/hosts`:
```
<INGRESS_IP>  php-api.local
```

Get the ingress IP:
```bash
kubectl get ingress php-api -o jsonpath='{.status.loadBalancer.ingress[0].ip}'
```

Test the API:
```bash
curl http://php-api.local/
curl http://php-api.local/health
curl http://php-api.local/hello
curl http://php-api.local/hello/John
```

## Prerequisites

- Docker installed
- Kubernetes cluster (Minikube, Kind, or other)
- kubectl configured
- Nginx Ingress Controller installed in cluster

### Install Nginx Ingress Controller (if not present)

```bash
# For Minikube
minikube addons enable ingress

# For other clusters
kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.8.2/deploy/static/provider/cloud/deploy.yaml
```

## What Was Created

1. **PHP Application**: A simple REST API with health, info, and greeting endpoints
2. **Docker Setup**: Multi-process container running nginx and php-fpm
3. **Kubernetes Manifests**: Complete deployment configuration with:
   - Deployment (2 replicas, resource limits, health probes)
   - Service (ClusterIP for internal routing)
   - Ingress (external access via nginx ingress controller)
